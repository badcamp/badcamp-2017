Feature: Security Feature
  In order to make sure our application is secure from the world
  I need to make sure that there are no keys for third-party services in configuration

  Scenario: Mailchimp Key in Config
    Given I am in the "config" path
    Then I should not see any keys for "mailchimp"

  Scenario: Stripe Key in Config
    Given I am in the "config" path
    Then I should not see any keys for "stripe"

  Scenario: Send Grid Key in Config
    Given I am in the "config" path
    Then I should not see any keys for "sendgrid"