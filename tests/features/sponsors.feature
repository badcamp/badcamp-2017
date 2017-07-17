Feature: Badcamp Sponsors

  Scenario: Sponsor view should appear on the homepage
    Given I am on the homepage
    Then I should see a "div.core-sponsors-block" element

  @api
  Scenario: Core level sponsor should appear in view
    Given I am viewing a "badcamp_sponsor":
      | title | Example Core Sponsor |
      | field_sponsor_level | Core Level |
    When I am on the homepage
    Then I should see a link pointing to "/sponsor/example-core-sponsor"

  @api
  Scenario: Sponsor Verify sponsor field existence
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/badcamp_sponsor"
    Then I should see "Logo"
    And I fill in the following:
      | Name | Example Core Sponsor |
      | About | Example Summary    |
      | Level | Core Level         |
      | URL   | http://example.com  |
      | Link text | Example Sponsor page |

