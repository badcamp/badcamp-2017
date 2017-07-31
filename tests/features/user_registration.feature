@drush @deleteUser
Feature: User Registration
  In order to test so basic user registration functionality
  As an anonymous user
  I need to be able to user registration

  Background:
    Given I am an anonymous user
    And I am on "/user/register"

  Scenario: Confirm User Registration is Enabled
    Then I should see a "form#user-register-form" element

  Scenario: Confirm user can fill out the user registration form
    When I fill in "Email address" with "joe@joesmith.com"
    And I fill in "Username" with "smithyboy143"
    And I fill in "First name" with "Joe"
    And I fill in "Last name" with "Smith"
    And I fill in "Drupal.org username" with "iheartthedrupal"
    And I select "America/Los Angeles" from "Time zone"
    And I press the "Create new account" button
    Then I should see the text "A welcome message with further instructions has been sent to your email address."

  Scenario: Confirm user cannot sign up more then once
    When I fill in "Email address" with "joe@joesmith.com"
    And I fill in "Username" with "smithyboy143"
    And I fill in "First name" with "Joe"
    And I fill in "Last name" with "Smith"
    And I fill in "Drupal.org username" with "iheartthedrupal"
    And I select "America/Los Angeles" from "Time zone"
    And I press the "Create new account" button
    Then I should see the text "The username smithyboy143 is already taken."

  @setNewUserPassword @cleanUp
  Scenario: Confirm newly registered user can login
    And I am on "/user/login"
    When I fill in "Username" with "smithyboy143"
    And I fill in "Password" with "mysecretpassword"
    And I press the "Log in" button
    Then I should see the text "Donate"