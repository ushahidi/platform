@permissions
Feature: Testing the Permissions API
    Scenario: Create a new permission
        Given that I want to make a new "Permission"
        And that the request "Authorization" header is "Bearer testadminuser"
        And that the request "data" is:
            """
            {
                "name":"Manager Admins"
            }
            """
        When I request "/permissions"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200
