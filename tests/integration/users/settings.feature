@oauth2Skip @users @usersettings
Feature: Testing the Form Settingss API

    Scenario: Creating a new Setting
        Given that I want to make a new "Setting"
        And that the request "data" is:
            """
            {
                "user_id":2,
                "config_key":"key",
                "config_value":"value"
            }
            """
        When I request "/users/2/settings"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Fail to create duplicate Setting
        Given that I want to make a new "Setting"
        And that the request "data" is:
            """
            {
                "user_id":2,
                "config_key":"key",
                "config_value":"value"
            }
            """
        When I request "/users/2/settings"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200
        # Now overwrite it
        Given that I want to make a new "Setting"
        And that the request "data" is:
            """
            {
                "user_id":2,
                "config_key":"key",
                "config_value":"value"
            }
            """
        When I request "/users/2/settings"
        Then the response is JSON
        And the response has a "errors" property
        Then the "errors.1.message" property equals "The user id, 2, and config key, key, already exist"
        Then the guzzle status code should be 422

    Scenario: Updating a Settings
        Given that I want to update a "Settings"
        And that the request "data" is:
            """
            {
                "user_id":2,
                "config_key":"updated key",
                "config_value":"updated value"
            }
            """
        And that its "id" is "1"
        When I request "/users/2/settings"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the response has a "config_key" property
        And the "config_key" property equals "updated key"
        Then the guzzle status code should be 200

    Scenario: Updating a non-existent Settings
        Given that I want to update a "Settings"
        And that the request "data" is:
            """
            {
                "user_id":2,
                "config_key":"updated key",
                "config_value":"updated value"
            }
            """
        And that its "id" is "59"
        When I request "/users/2/settings"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    @resetFixture @user_settings
    Scenario: Listing All Settings for a user
        Given that I want to get all "Settings"
        When I request "/users/2/settings"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    Scenario: Finding a Settings
        Given that I want to find a "Settings"
        And that its "id" is "1"
        When I request "/users/2/settings"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "config_value" property
        And the "config_value" property equals "******alue"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Settings
        Given that I want to find a "Settings"
        And that its "id" is "999"
        When I request "/users/2/settings"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Settings
        Given that I want to delete a "Settings"
        And that its "id" is "1"
        When I request "/users/2/settings"
        Then the guzzle status code should be 200

    Scenario: Deleting a non-existent Settings
        Given that I want to delete a "Settings"
        And that its "id" is "998"
        When I request "/users/2/settings"
        And the response has a "errors" property
        Then the guzzle status code should be 404

