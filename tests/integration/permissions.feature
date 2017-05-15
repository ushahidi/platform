@permissions
Feature: Testing the Permissions API
    Scenario: List available permissions
        Given that I want to get all "Permissions"
        And that the oauth token is "testadminuser"
        When I request "/permissions"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "4"
        Then the guzzle status code should be 200

    Scenario: List a single permission
        Given that I want to get all "Permissions"
        And that the oauth token is "testadminuser"
        When I request "/permissions/3"
        Then the response is JSON
        And the response has a "name" property
        Then the guzzle status code should be 200

    Scenario: Admin cannot create new permission
        Given that I want to make a new "Permission"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {
                "name":"Manage Admins"
            }
            """
        When I request "/permissions"
        Then the guzzle status code should be 405


