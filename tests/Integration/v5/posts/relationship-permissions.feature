@post @rolesEnabled
Feature: I can get a post with a category I can't see
  @get
  Scenario: Finding a Post with a tag only admins and members can see [as anonymous]
    Given that I want to find a "Post"
    And that the api_url is "api/v5"
    And that its "id" is "99"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.post_content.0.fields.6.value" property count is "2"
    And the response does not have a "result.post_content.0.fields.6.value.0._ush_hidden" property
    And the response has a "result.post_content.0.fields.6.value.1._ush_hidden" property
    Then the guzzle status code should be 200
  @get
  Scenario: Finding a Post with a tag only admins and members can see [as admin]
    Given that I want to find a "Post"
    And that the api_url is "api/v5"
    And that the oauth token is "testadminuser"
    And that its "id" is "99"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.post_content.0.fields.6.value" property count is "2"
    And the response does not have a "result.post_content.0.fields.6.value.0._ush_hidden" property
    And the response does not have a "result.post_content.0.fields.6.value.1._ush_hidden" property
    Then the guzzle status code should be 200
