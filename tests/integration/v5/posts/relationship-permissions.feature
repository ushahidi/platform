@post @rolesEnabled
Feature: I can get a post with a category I can't see
  @get
  Scenario: Finding a Post with a tag only admins and members can see
    Given that I want to find a "Post"
    And that the api_url is "api/v5"
    And that its "id" is "99"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    Then the guzzle status code should be 200
