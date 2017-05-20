Feature: Blog Testing

  @api
  Scenario: Verify blog page exists
    Given "blog" content:
    | title                                   | body                            | tags                                    |
    | Sa | I am writing a sample blog post | Drupal Planet, Tag Sample A, Other Tag  |
    When I am on "/news"
    Then I should get a "200" HTTP response

  @api
  Scenario: Verify the blog posts are in decending order
    Given "blog" content:
    | title     | body                            | tags                                    |
    | Article 1 | Body of Article 1               | Drupal Planet                           |
    | Article 2 | Body of Sample Article 2        | Training                                |
    | Article 3 | Sample Note                     |                                         |
    | Article 4 | Note                            | Drupal Planet                           |
    When I am on "/news"
    Then I should see the text "Article 1"
    And I should see the text "Article 2"
    And I should see the text "Article 3"
    And I should see the text "Article 4"
    And "Article 4" should precede "Article 3" for the query ".blog-list-page .views-row article h2"
    And "Article 3" should precede "Article 2" for the query ".blog-list-page .views-row article h2"
    And "Article 2" should precede "Article 1" for the query ".blog-list-page .views-row article h2"

  @api
  Scenario: Verify the Drupal Planet feed exists
    Given I am an anonymous user
    And I am on "/drupal-planet"
    And I click "Subscribe to Drupal Planet"
    Then I should get a "200" HTTP response

  @api
  Scenario: Verify the url to Drupal Planet feed exists
    Given I am an anonymous user
    And I am on "/drupal-planet/feed"
    Then I should get a "200" HTTP response

  @api
  Scenario: Create Blog post as user with appropriate permission
    Given I am logged in as a user with the "create blog content" permission
    And I am on "/node/add/blog"
    And I fill in "Title" with "Test Article A"
    And I fill in "Body" with "Sample article post"
    And I fill in "Tags" with "Drupal Planet, Sample Tag, New Tag, Tag1"
    And I press the "Save and publish" button
    Then I should see "Test Article A" in the "#block-badcamp2017-page-title" element
    And I should get a "200" HTTP response

  @api
  Scenario: Confirm a general authenticated user cannot create Blog posts
    Given I am logged in as a user with the "authenticate user" role
    And I am on "/node/add/blog"
    Then I should see the text "Access Denied"

  @api
  Scenario: Confirm an anonymous user cannot create Blog posts
    Given I am an anonymous user
    And I am on "/node/add/blog"
    Then I should see the text "Access Denied"