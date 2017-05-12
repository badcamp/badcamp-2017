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
    $slack_message = 'New code is detected on GitHub! Kicking off a new build - https://circleci.com/gh/badcamp/badcamp-2017/' . $argv[2];
    _slack_tell( $slack_message, $slack_channel, $slack_agent, $slack_icon, $slack_color);
    break;
  case 'pantheon_merge':
    $slack_agent = 'Pantheon-Merge';
    $slack_icon = 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/pantheon.png';
    $slack_color = '#EFD01B';
    $slack_message = "Merging `". $argv[2] . "` environment into Pantheon `dev` environment...";
    _slack_tell( $slack_message, $slack_channel, $slack_agent, $slack_icon, $slack_color);
    break;
  case 'pantheon_create':
    $slack_agent = 'Pantheon-Create';
    $slack_icon = 'http://live-drupalcon-github-magic.pantheonsite.io/sites/default/files/icons/pantheon.png';
    $slack_color = '#1EFD01B';
    $slack_message = "Creating Pantheon environment `". $argv[2] . "`...";
    _slack_tell( $slack_message, $slack_channel, $slack_agent, $slack_icon, $slack_color);
    break;
}
