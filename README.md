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
To setup a local development environment, checkout the code from this repository and then run composter install to get everything up to date. Do not commit the vendor directory to the project (there is a .gitignore to help).
#### PR Workflow
If you want to make a change, you can commit it to an individual branch and the CI will automatically create a Multidev with the same name and push the code there.
