@oauth2Skip
Feature: Testing the Config API

    Scenario: POSTing a config value should fail
        Given that I want to make a new "Config"
        And that the request "data" is:
            """
            {
                "id":"test",
                "test":"Test value"
            }
            """
        When I request "/config"
        Then the guzzle status code should be 405

    Scenario: Updating a Config
        Given that I want to update a "Config"
        And that the request "data" is:
            """
            {
                "testkey":"i am a teapot?"
            }
            """
        When I request "/config/test"
        Then the response is JSON
        And the "id" property equals "test"
        And the "testkey" property equals "i am a teapot?"
        Then the guzzle status code should be 200

    Scenario: Adding to Config with PUT
        Given that I want to update a "Config"
        And that the request "data" is:
            """
            {
                "nothing": "new test value",
                "json_object_test": {
                    "boolean": true,
                    "null": null,
                    "array": ["a","b","c"],
                    "number": 1234
                }
            }
            """
        When I request "/config/test"
        Then the response is JSON
        And the "id" property equals "test"
        And the "nothing" property equals "new test value"
        And the "json_object_test.number" property equals "1234"
        And the "json_object_test.array" property contains "a"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing All Configs
        Given that I want to get all "Configs"
        When I request "/config"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search for Configs by Group
        Given that I want to get all "Configs"
        And that the request "query string" is "groups[]=test&groups[]=site"
        When I request "/config"
        Then the response is JSON
        And the "count" property equals "2"
        And the "results.0.id" property equals "test"
        And the "results.1.id" property equals "site"
        Then the guzzle status code should be 200

    Scenario: Finding a Config
        Given that I want to find a "Config"
        When I request "/config/test"
        Then the response is JSON
        And the "id" property equals "test"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Config
        Given that I want to find a "Config"
        When I request "/config/nonexistingconfigshouldfail"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Config
        Given that I want to delete a "Config"
        When I request "/config/test"
        Then the guzzle status code should be 405
