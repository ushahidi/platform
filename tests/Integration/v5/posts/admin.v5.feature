@post @rolesEnabled
Feature: Testing the Posts API
  @resetFixture @search
  Scenario: Getting all posts as an admin
    Given that I want to get all "Posts"
    And that the api_url is "api/v5"
    And that the oauth token is "testadminuser"
    When I request "/posts"
    Then the response is JSON
    And the response has a "count" property
    And the type of the "count" property is "numeric"
    And the "count" property equals "20"
    And the "meta.total" property equals "33"
    Then the guzzle status code should be 200

  @get
  Scenario: View post with restricted data as admin gets full info
    Given that I want to find a "Post"
    And that the api_url is "api/v5"
    And that the oauth token is "testadminuser"
    And that its "id" is "1690"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.title" property equals "Post published to members"
    And the response has a "result.post_content" property
    And the "result.post_content.1.fields.3.value.value.lat" property equals "26.2135"
    And the "result.post_content.1.fields.3.value.value.lon" property equals "10.1235"
    And the "result.post_content.1.fields.4.value.value" property equals "2014-09-29T15:11:46+0000"
    And the "result.post_date" property equals "2014-09-29T14:10:16+0000"
    And the "result.created" property equals "2014-09-29T21:10:16+0000"
    And the "result.updated" property is empty
    And the "result.user_id" property equals "3"
    And the "result.author_email" property equals "test@ushahidi.com"
    And the "result.author_realname" property equals "Test Name"
    Then the guzzle status code should be 200

  @resetFixture @search
  Scenario: Getting one draft post for admin user
    Given that I want to find a "Post"
    And that its "id" is "1800"
    And that the oauth token is "testadminuser"
    And that the api_url is "api/v5"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result" property
    And the "result.id" property equals "1800"
    And the "result.status" property equals "draft"
    And the response has a "result.translations.es.title" property
    And the "result.translations.es.title" property equals "DRAFT Title field translated - ES"
    And the "result.translations.es.content" property equals "DRAFT Content field translated - ES"
    Then the guzzle status code should be 200

  @resetFixture @search
  Scenario: Getting one published post for admin user
    Given that I want to find a "Post"
    And that its "id" is "1801"
    And that the oauth token is "testadminuser"
    And that the api_url is "api/v5"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result" property
    And the "result.id" property equals "1801"
    And the "result.status" property equals "published"
    And the response has a "result.translations.es.title" property
    And the "result.translations.es.title" property equals "PUBLISHED Title field translated - ES"
    And the "result.translations.es.content" property equals "PUBLISHED Content field translated - ES"
    And the "result.post_content.2.fields.0.type" property equals "varchar"
    And the "result.post_content.2.fields.0.input" property equals "checkbox"
    And the "result.post_content.2.fields.0.value.value.0" property equals "ground_search_ft"
    Then the guzzle status code should be 200
