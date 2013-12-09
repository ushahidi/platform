@oauth2Skip
Feature: Testing the Config API

    Scenario: POSTing a config value should fail
        Given that I want to make a new "Config"
        And that the request "data" is:
            """
            {
                "group_name":"site",
                "config_key":"test",
                "config_value":"Test value"
            }
            """
        When I request "/config"
        Then the guzzle status code should be 405

    Scenario: Updating a Config
        Given that I want to update a "Config"
        And that the request "data" is:
            """
            {
                "config_value":"Updated value"
            }
            """
        And that its "id" is "test"
        When I request "/config/site"
        Then the response is JSON
        And the "group_name" property equals "site"
        And the "config_key" property equals "test"
        And the "config_value" property equals "Updated value"
        Then the guzzle status code should be 200

    Scenario: Creating a new Config with PUT
        Given that I want to update a "Config"
        And that the request "data" is:
            """
            {
                "config_value":"new test value"
            }
            """
        And that its "id" is "nothing"
        When I request "/config/site"
        Then the response is JSON
        And the "group_name" property equals "site"
        And the "config_key" property equals "nothing"
        And the "config_value" property equals "new test value"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing All Configs
        Given that I want to get all "Configs"
        When I request "/config"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "4"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Configs in a group
        Given that I want to get all "Configs"
        When I request "/config/test"
        Then the response is JSON
        And the "count" property equals "1"
        And the "results.0.config_key" property equals "testkey"
        Then the guzzle status code should be 200

    Scenario: Finding a Config
        Given that I want to find a "Config"
        And that its "id" is "site_name"
        When I request "/config/site/"
        Then the response is JSON
        And the "group_name" property equals "site"
        And the "config_key" property equals "site_name"
        Then the guzzle status code should be 200

#    Scenario: Finding a non-existent Config
#        Given that I want to find a "Config"
#        And that its "id" is "nothing"
#        When I request "/config/site"
#        Then the response is JSON
#        And the response has a "errors" property
#        Then the guzzle status code should be 404

    Scenario: Deleting a Config
        Given that I want to delete a "Config"
        And that its "id" is "site_name"
        When I request "/config/site"
        Then the guzzle status code should be 405

