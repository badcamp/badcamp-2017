# BADCamp 2017 Magical Website

[![CircleCI](https://circleci.com/gh/badcamp/badcamp-2017.svg?style=svg)](https://circleci.com/gh/populist/badcamp-2017)
[![Pantheon badcamp-2017](https://img.shields.io/badge/pantheon-badcamp_2017-yellow.svg)](https://dashboard.pantheon.io/sites/8d658997-7a61-4db8-9be8-0f16b8b62022#dev/code)
[![Dev Site badcamp-2017](https://img.shields.io/badge/site-badcamp_2017-blue.svg)](http://dev-badcamp-2017.pantheonsite.io/)

# Developer Notes
Pick a box. It's contents will help you on your way. -- Toad
#### Continous Integration
Every commit on this project will be deployed automatically to Pantheon  as per this [Circle CI SCript](https://github.com/badcamp/badcamp-2017/blob/master/circle.yml).
#### Configuration Changes
If you want to change configuration, commit your YML files to the /config directory and the CI will automatically import that configuration as part of moving the site to Pantheon.
#### Module Additions
If you want to add a module (or other extension), simply add it in composer to the repo and it will automatically be assembled as part of moving the site to Pantheon.
#### CSS Compilation
There is currently no CSS compilation setup as part of the CI build, but this will be done as soon as we pick the tooling that everyone wants to use.
#### Work on GitHub, Not on Pantheon
Please do all of your code commits directly to GitHub. Do not commit anything directly to Pantheon.
#### Local Development
To setup a local development environment, checkout the code from this repository and then run composer install to get everything up to date. Do not commit the vendor directory to the project (there is a .gitignore to help).

Speaking of picking a box...
##### MAMP Pro Users
After you have run composer install:
1. Set up a new site **Important:** Your document root is the web subdirectory of the repo. You might want to have MAMP set up the database for you while creating the site, you're going to need one anyway.
2. Get a dump of the database from the dev environment of the Pantheon site (Database / Files -> Export). Why? becasue Drupal 8 sites have a uuid that is in the configuration and if you did your own fresh install you would have a different one in your config from the site we're working on that already has been installed.
3. Import the downloaded database into your newly created local site database (Sequel Pro is a nice free tool or from command line: ```mysql -uMYSQLUSERNAME -p NAME_OF_YOURDB < PATH_TO_YOUR_DOWNLOADED_SQL_DUMP_FILE```).
4. Set up you local settings.local.php with a setting for 'hash_salt' and database connection.
```PHP
$settings['hash_salt'] = 'JUST A BUNCH OF RANDOM CHARACTERS GO HERE';

$databases['default']['default'] = array (
  'database' => 'NAME_OF_DB',
  'username' => 'DB_USERNAME',
  'password' => 'DB_PASSWORD',
  'host' => 'localhost',
  'port' => '3306',
  'driver' => 'mysql',
  'prefix' => '',
  'collation' => 'utf8mb4_general_ci',
);
```
5. Add a .htaccess file to the web directory (need to do this to get to admin paths, etc.). You can just grab this from any other clean Drupal 8 codebase.
6. On the command line import the configuration using drush **from the web directory**.
```text
#drush cim -y
```
7. Profit! I mean, you're prolly gonna have to raise your rates now because that took a while, but, yeah... profit!
8. Note: want to login as an admin? from the command line in web directory: ```# drush uli uri=WHATEVER_YOU_SET_NAME_TO```
##### Kalabox users
Currently this site's architecture does not play nice with Kalabox. Mike Pirog is efforting some solution to the issue and once resolved there will be instructions placed here.

#### PR Workflow
If you want to make a change, you can commit it to an individual branch and the CI will automatically create a Multidev with the same name and push the code there.
