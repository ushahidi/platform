@post @rolesEnabled
Feature: Testing the v5 posts API for anonymous users
  @resetFixture @search
  Scenario: Getting all published posts for anonymous user
    Given that I want to get all "Posts"
    And that the api_url is "api/v5"
    When I request "/posts"
    Then the response is JSON
    And the response has a "count" property
    And the type of the "count" property is "numeric"
    And the "count" property equals "20"
    And the "meta.total" property equals "20"
    Then the guzzle status code should be 200
  @resetFixture @search
  Scenario: Getting one draft post for anonymous user
    Given that I want to find a "Post"
    And that its "id" is "1800"
    And that the api_url is "api/v5"
    When I request "/posts"
    Then the response is JSON
    And the response has a "error" property
    Then the guzzle status code should be 404
  @resetFixture @search
  Scenario: Getting one published post for anonymous user
    Given that I want to find a "Post"
    And that its "id" is "1801"
    And that the api_url is "api/v5"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result" property
    And the "result.id" property equals "1801"
    And the "result.status" property equals "published"
    And the response has a "result.translations.es.title" property
    And the "result.translations.es.title" property equals "PUBLISHED Title field translated - ES"
    And the "result.translations.es.content" property equals "PUBLISHED Content field translated - ES"
    And the response does not have a "result.post_content.2.fields.0.type" property
    Then the guzzle status code should be 200


