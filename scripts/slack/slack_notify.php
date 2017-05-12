<?php

// Load Slack helper functions
require_once( dirname( __FILE__ ) . '/slack_helper.php' );

// Assemble the Arguments
$slack_type = $argv[1]; // Argument One
$slack_channel = getenv('SLACK_CHANNEL');

switch($slack_type) {
  case 'circle_start':
    $slack_agent = 'CircleCI';
    $slack_icon = 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/circle.png';
    $slack_color = '#229922';
    $slack_message = 'Time to check for new updates! Kicking off a new build...';
    _slack_tell( $slack_message, $slack_channel, $slack_agent, $slack_icon, $slack_color);
    $slack_message = array();
    $slack_message['Build Environment'] = $argv[3];
    $slack_message['Build URL'] = 'https://circleci.com/gh/badcamp/badcamp-2017/' . $argv[2];
    _slack_tell( $slack_message, $slack_channel, $slack_agent, $slack_icon, $slack_color);
    break;
  case 'pantheon_merge':
    $slack_agent = 'Pantheon';
    $slack_icon = 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/terminus2.png';
    $slack_color = '#1ec503';
    $slack_message = "Merging `". $argv[2] . "` environment into Pantheon `dev` environment...";
    _slack_tell( $slack_message, $slack_channel, $slack_agent, $slack_icon, $slack_color);
    $slack_message = array();
    $slack_message['Operation'] = 'terminus build-env:merge';
    $slack_message['Environment'] = '`' . $argv[2] . '`';
    _slack_tell( $slack_message, $slack_channel, $slack_agent, $slack_icon, $slack_color);
    break;
  case 'pantheon_create':
    $slack_agent = 'Pantheon';
    $slack_icon = 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/terminus2.png';
    $slack_color = '#1ec503';
    $slack_message = "Create Pantheon environment `". $argv[2] . "`...";
    _slack_tell( $slack_message, $slack_channel, $slack_agent, $slack_icon, $slack_color);
    $slack_message = array();
    $slack_message['Operation'] = 'terminus build-env:create';
    $slack_message['Environment'] = '`' . $argv[2] . '`';
    _slack_tell( $slack_message, $slack_channel, $slack_agent, $slack_icon, $slack_color);
    break;
}
