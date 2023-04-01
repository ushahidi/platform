@permissions
Feature: Testing the Permissions API
    Scenario: List available permissions
        Given that I want to get all "Permissions"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        When I request "/permissions"
        Then the response is JSON
        And the response has a "meta.total" property
        And the type of the "meta.total" property is "numeric"
        And the "meta.total" property equals "8"
        Then the guzzle status code should be 200

    Scenario: List a single permission
        Given that I want to get all "Permissions"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        When I request "/permissions/3"
        Then the response is JSON
        And the response has a "result.name" property
        Then the guzzle status code should be 200

    Scenario: Admin cannot create new permission
        Given that I want to make a new "Permission"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        And that the request "data" is:
            """
            {
                "name":"Manage Admins"
            }
            """
        When I request "/permissions"
        Then the guzzle status code should be 405


