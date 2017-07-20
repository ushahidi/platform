@resetFixture @tos 
Feature: Testing the Tos API

    Scenario: Create a ToS entry
        Given that I want to make a new "tos"
        And that the request "Authorization" header is "testbasicuser"
        And that the request "data" is:
            """
            {
                "tos_version_date":"1500059540"
            }
            """
        When I request "/tos"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Getting a ToS entry
        Given that I want to find a "Tos"
        And that the request "Authorization" header is "testbasicuser"
        When I request "/tos"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "tos_version_date" property equals "1500059540"
        Then the guzzle status code should be 200
