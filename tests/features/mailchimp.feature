Feature: Mailchimp signup form

  Scenario: Mailchimp signup form should appear on the homepage
    Given I am on the homepage
    Then I should see a "form#mailchimp-signup-subscribe-block-badcamp-newsletter-form" element