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
  $config['sendgrid_integration.settings']['apikey'] = $key_data['sendgrid_api'];
  $config['stripe_api.settings']['mode'] = 'live';
}
else {
  // We aren't in prod, load a fallback or null key.
  $json_text = file_get_contents('sites/default/files/private/badcamp_keys.json');
  $key_data = json_decode($json_text, TRUE);
  $config['mailchimp.settings']['api_key'] = $key_data['mailchimp_key'];
  $config['sendgrid_integration.settings']['apikey'] = $key_data['sendgrid_api'];
}

// Require HTTPS
if (isset($_SERVER['PANTHEON_ENVIRONMENT']) && ($_SERVER['HTTPS'] === 'OFF') && (php_sapi_name() != "cli")) {
  if (!isset($_SERVER['HTTP_X_SSL']) || (isset($_SERVER['HTTP_X_SSL']) && $_SERVER['HTTP_X_SSL'] != 'ON')) {
    header('HTTP/1.0 301 Moved Permanently');
    header('Location: https://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
  }
}

// Require 2017.badcamp.net Domain
if (isset($_SERVER['PANTHEON_ENVIRONMENT']) && ($_SERVER['PANTHEON_ENVIRONMENT'] === 'live') && (php_sapi_name() != "cli")) {
  if ($_SERVER['HTTP_HOST'] != '2017.badcamp.net' || !isset($_SERVER['HTTP_X_SSL']) || $_SERVER['HTTP_X_SSL'] != 'ON' ) {
    header('HTTP/1.0 301 Moved Permanently');
    header('Location: https://2017.badcamp.net'. $_SERVER['REQUEST_URI']);
    exit();
  }
}

// Prevent Config Changes in Production
if (isset($_SERVER['PANTHEON_ENVIRONMENT']) && ($_SERVER['PANTHEON_ENVIRONMENT'] === 'live')) {
  $settings['config_readonly'] = TRUE;
}
