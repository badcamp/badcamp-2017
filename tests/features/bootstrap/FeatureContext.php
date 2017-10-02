<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Drupal\DrupalExtension\Context\MinkContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Hook\Scope\AfterStepScope;
use PHPUnit_Framework_Assert;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Tester\Exception\PendingException;

/**
 * Define application features from the specific context.
 */
class FeatureContext extends RawDrupalContext implements Context, SnippetAcceptingContext {
  /**
   * Initializes context.
   * Every scenario gets its own context object.
   *
   * @param array $parameters
   *   Context parameters (set them in behat.yml)
   */
  public function __construct(array $parameters = []) {
    // Initialize your context here
  }

  /** @var \Drupal\DrupalExtension\Context\MinkContext */
  private $minkContext;
  /** @BeforeScenario */
  public function gatherContexts(BeforeScenarioScope $scope)
  {
      $environment = $scope->getEnvironment();
      $this->minkContext = $environment->getContext('Drupal\DrupalExtension\Context\MinkContext');
  }

//
// Place your definition and hook methods here:
//
//  /**
//   * @Given I have done something with :stuff
//   */
//  public function iHaveDoneSomethingWith($stuff) {
//    doSomethingWith($stuff);
//  }
//

    /**
     * Fills in form field with specified id|name|label|value
     * Example: And I enter the value of the env var "TEST_PASSWORD" for "edit-account-pass-pass1"
     *
     * @Given I enter the value of the env var :arg1 for :arg2
     */
    public function fillFieldWithEnv($value, $field)
    {
        $this->minkContext->fillField($field, getenv($value));
    }

    /**
     * @Given I wait for the progress bar to finish
     */
    public function iWaitForTheProgressBarToFinish() {
      $this->iFollowMetaRefresh();
    }

    /**
     * @Given I follow meta refresh
     *
     * https://www.drupal.org/node/2011390
     */
    public function iFollowMetaRefresh() {
      while ($refresh = $this->getSession()->getPage()->find('css', 'meta[http-equiv="Refresh"]')) {
        $content = $refresh->getAttribute('content');
        $url = str_replace('0; URL=', '', $content);
        $this->getSession()->visit($url);
      }
    }

    /**
     * @Given I have wiped the site
     */
    public function iHaveWipedTheSite()
    {
        $site = getenv('TERMINUS_SITE');
        $env = getenv('TERMINUS_ENV');

        passthru("terminus env:wipe $site.$env --yes");
    }

    /**
     * @Given I have reinstalled
     */
    public function iHaveReinstalled()
    {
        $site = getenv('TERMINUS_SITE');
        $env = getenv('TERMINUS_ENV');
        $site_name = getenv('TEST_SITE_NAME');
        $site_mail = getenv('ADMIN_EMAIL');
        $admin_password = getenv('ADMIN_PASSWORD');

        passthru("terminus --yes drush $site.$env -- --yes site-install standard --site-name=\"$site_name\" --site-mail=\"$site_mail\" --account-name=admin --account-pass=\"$admin_password\"'");
    }

    /**
     * @Given I have run the drush command :arg1
     */
    public function iHaveRunTheDrushCommand($arg1)
    {
        $site = getenv('TERMINUS_SITE');
        $env = getenv('TERMINUS_ENV');

        $return = '';
        $output = array();
        exec("terminus drush $site.$env -- " . $arg1, $output, $return);
        // echo $return;
        // print_r($output);

    }

    /**
     * @Given I have committed my changes with comment :arg1
     */
    public function iHaveCommittedMyChangesWithComment($arg1)
    {
        $site = getenv('TERMINUS_SITE');
        $env = getenv('TERMINUS_ENV');

        passthru("terminus --yes $site.$env env:commit --message='$arg1'");
    }

    /**
     * @Given I have exported configuration
     */
    public function iHaveExportedConfiguration()
    {
        $site = getenv('TERMINUS_SITE');
        $env = getenv('TERMINUS_ENV');

        $return = '';
        $output = array();
        exec("terminus drush $site.$env -- config-export -y", $output, $return);
    }

    /**
     * @Given I wait :seconds seconds
     */
    public function iWaitSeconds($seconds)
    {
        sleep($seconds);
    }

    /**
     * @Given I wait :seconds seconds or until I see :text
     */
    public function iWaitSecondsOrUntilISee($seconds, $text)
    {
        $errorNode = $this->spin( function($context) use($text) {
            $node = $context->getSession()->getPage()->find('named', array('content', $text));
            if (!$node) {
              return false;
            }
            return $node->isVisible();
        }, $seconds);

        // Throw to signal a problem if we were passed back an error message.
        if (is_object($errorNode)) {
          throw new Exception("Error detected when waiting for '$text': " . $errorNode->getText());
        }
    }

    // http://docs.behat.org/en/v2.5/cookbook/using_spin_functions.html
    // http://mink.behat.org/en/latest/guides/traversing-pages.html#selectors
    public function spin ($lambda, $wait = 60)
    {
        for ($i = 0; $i <= $wait; $i++)
        {
            if ($i > 0) {
              sleep(1);
            }

            $debugContent = $this->getSession()->getPage()->getContent();
            file_put_contents("/tmp/mink/debug-" . $i, "\n\n\n=================================\n$debugContent\n=================================\n\n\n");

            try {
                if ($lambda($this)) {
                    return true;
                }
            } catch (Exception $e) {
                // do nothing
            }

            // If we do not see the text we are waiting for, fail fast if
            // we see a Drupal 8 error message pane on the page.
            $node = $this->getSession()->getPage()->find('named', array('content', 'Error'));
            if ($node) {
              $errorNode = $this->getSession()->getPage()->find('css', '.messages--error');
              if ($errorNode) {
                return $errorNode;
              }
              $errorNode = $this->getSession()->getPage()->find('css', 'main');
              if ($errorNode) {
                return $errorNode;
              }
              return $node;
            }
        }

        $backtrace = debug_backtrace();

        throw new Exception(
            "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n" .
            $backtrace[1]['file'] . ", line " . $backtrace[1]['line']
        );

        return false;
    }

    /**
     * @AfterStep
     */
    public function afterStep(AfterStepScope $scope)
    {
        // Do nothing on steps that pass
        $result = $scope->getTestResult();
        if ($result->isPassed()) {
            return;
        }

        // Otherwise, dump the page contents.
        $session = $this->getSession();
        $page = $session->getPage();
        $html = $page->getContent();
        $html = static::trimHead($html);

        print "::::::::::::::::::::::::::::::::::::::::::::::::\n";
        print $html . "\n";
        print "::::::::::::::::::::::::::::::::::::::::::::::::\n";
    }

    /**
     * Remove everything in the '<head>' element except the
     * title, because it is long and uninteresting.
     */
    protected static function trimHead($html)
    {
        $html = preg_replace('#\<head\>.*\<title\>#sU', '<head><title>', $html);
        $html = preg_replace('#\</title\>.*\</head\>#sU', '</title></head>', $html);
        return $html;
    }

    /**
     * Reference: http://neverstopbuilding.com/simple-method-for-checking-for-order-with-behat
     * @Then /^"([^"]*)" should precede "([^"]*)" for the query "([^"]*)"$/
     */
    public function shouldPrecedeForTheQuery($textBefore, $textAfter, $cssQuery)
    {
      $items = array_map(
        function ($element) {
          return $element->getText();
        },
        $this->getSession()->getPage()->findAll('css', $cssQuery)
      );
      PHPUnit_Framework_Assert::assertGreaterThan(
        array_search($textBefore, $items),
        array_search($textAfter, $items),
        "$textBefore does not proceed $textAfter"
      );
    }

    /**
     * @BeforeScenario @setNewUserPassword
     */
    public function setNewUserPassword()
    {
      $output = $this->getDriver('drush')->upwd('--password="mysecretpassword"','smithyboy143');
      echo $output;
    }

    /**
     * @AfterScenario @cleanUp
     */
    public function cleanUp()
    {
      $this->users[] = (object)array('name' => 'smithyboy143');
    }

  /**
   * @Then I should see a link pointing to :arg1
   */
  public function iShouldSeeALinkPointingTo($arg1)
  {
    $links = $this->getSession()->getPage()->findAll('css', 'a');
    /** @var \Behat\Mink\Element\NodeElement $link */
    foreach ($links as $link) {
      ($link->getAttribute('href'));
      if ($link->getAttribute('href') == $arg1) {
        return;
      }
    }
    throw new \Exception(strtr('No links with path @path were found', ['@path' => $arg1]));
  }

  /**
   * @Given I am in the :path path
   */
  public function iAmInThePath($path)
  {
    $this->path = $path;
  }

  /**
   * @Then I should not see any keys for :esrvice
   */
  public function iShouldNotSeeAnyKeysFor($service)
  {
    switch($service) {
      case 'mailchimp':
        $value = \Symfony\Component\Yaml\Yaml::parse(file_get_contents( __DIR__  . '/../../../' . $this->path . '/mailchimp.settings.yml'));
        PHPUnit_Framework_Assert::assertEmpty($value['api_key'], "Mailchimp does not contain any keys.");
        break;
      case 'stripe':
        $value = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(  __DIR__  . '/../../../' . $this->path . '/stripe_api.settings.yml'));

        $file1 = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(  __DIR__  . '/../../../' . $this->path . '/key.key.' . $value['test_secret_key'] . '.yml'));
        PHPUnit_Framework_Assert::assertEquals($file1['key_provider'], 'file', 'Stripe test_secret_key does not contain a key');
        PHPUnit_Framework_Assert::assertEquals($file1['key_input'], 'none', 'Stripe test_secret_key does not contain a key');

        $file2 = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(  __DIR__  . '/../../../' . $this->path . '/key.key.' . $value['test_public_key'] . '.yml'));
        PHPUnit_Framework_Assert::assertEquals($file2['key_provider'], 'file', 'Stripe test_public_key does not contain a key');
        PHPUnit_Framework_Assert::assertEquals($file1['key_input'], 'none', 'Stripe test_public_key does not contain a key');

        $file3 = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(  __DIR__  . '/../../../' . $this->path . '/key.key.' . $value['live_secret_key'] . '.yml'));
        PHPUnit_Framework_Assert::assertEquals($file3['key_provider'], 'file', 'Stripe live_secret_key does not contain a key');
        PHPUnit_Framework_Assert::assertEquals($file1['key_input'], 'none', 'Stripe live_secret_key does not contain a key');

        $file4 = \Symfony\Component\Yaml\Yaml::parse(file_get_contents(  __DIR__  . '/../../../' . $this->path . '/key.key.' . $value['live_public_key'] . '.yml'));
        PHPUnit_Framework_Assert::assertEquals($file4['key_provider'], 'file', 'Stripe live_public_key does not contain a key');
        PHPUnit_Framework_Assert::assertEquals($file1['key_input'], 'none', 'Stripe live_public_key does not contain a key');

        break;
      case 'sendgrid':
        $value = \Symfony\Component\Yaml\Yaml::parse(file_get_contents( __DIR__  . '/../../../' . $this->path . '/sendgrid_integration.settings.yml'));
        PHPUnit_Framework_Assert::assertEmpty($value['apikey'], "Sendgrid does not contain any keys.");
        break;
      default:
        throw new PendingException();
    }
  }
}
