<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all envrionments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * Place the config directory outside of the Drupal root.
 */
$config_directories = array(
  CONFIG_SYNC_DIRECTORY => dirname(DRUPAL_ROOT) . '/config',
);

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

/**
 * Always install the 'standard' profile to stop the installer from
 * modifying settings.php.
 *
 * See: tests/installer-features/installer.feature
 */
$settings['install_profile'] = 'standard';

/**
 * Load in the api keys from the JSON file in the private files.
 */
if (isset($_ENV['PANTHEON_ENVIRONMENT']) && $_ENV['PANTHEON_ENVIRONMENT'] == 'live') {
  $json_text = file_get_contents('sites/default/files/private/badcamp_keys.json');
  $key_data = json_decode($json_text, TRUE);
  $config['mailchimp.settings']['api_key'] = $key_data['mailchimp_key'];
  $config['stripe_api.settings']['test_secret_key'] = $key_data['stripe_test_key_secret'];
  $config['stripe_api.settings']['test_public_key'] = $key_data['stripe_test_key_pub'];
  $config['stripe_api.settings']['live_secret_key'] = $key_data['stripe_live_key_secret'];
  $config['stripe_api.settings']['live_public_key'] = $key_data['stripe_live_key_pub'];
}
else {
  // We aren't in prod, load a fallback or null key.
  $json_text = file_get_contents('sites/default/files/private/badcamp_keys.json');
  $key_data = json_decode($json_text, TRUE);
  $config['mailchimp.settings']['api_key'] = $key_data['mailchimp_key'];
  $config['stripe_api.settings']['test_secret_key'] = $key_data['stripe_test_key_secret'];
  $config['stripe_api.settings']['test_public_key'] = $key_data['stripe_test_key_pub'];
}