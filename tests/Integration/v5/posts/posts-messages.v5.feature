@post @rolesEnabled
Feature: SMS originated posts have message and contact properties
  @resetFixture @get
  Scenario: Getting post originated from SMS message
    Given that I want to find a "Post"
    And that the api_url is "api/v5"
    And that its "id" is "9999"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the response has a "result.message" property
    And the response has a "result.contact" property
