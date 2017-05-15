@roles
Feature: Testing the Roles API
    Scenario: Create a new role
        Given that I want to make a new "Role"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {
                "name":"editor",
                "display_name":"Editor"
            }
            """
        When I request "/roles"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Create a new role with permissions
        Given that I want to make a new "Role"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {
                "name":"supervisor",
                "display_name":"Supervisor",
                "permissions":["Manage Users"]
            }
            """
        When I request "/roles"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "permissions" property
        And the "permissions.0" property equals "Manage Users"
        Then the guzzle status code should be 200

    Scenario: Assign a permission to a role
        Given that I want to update a "Role"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {
                "permissions":["Manage Users", "Manage Settings"]
            }
            """
        And that its "id" is "4"
        When I request "/roles"
        And the response has a "permissions" property
        And the "permissions.0" property equals "Manage Users"
        And the "permissions.1" property equals "Manage Settings"
        Then the guzzle status code should be 200

     Scenario: Change permission of a role
        Given that I want to update a "Role"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {
                "permissions":["Manage Posts"]
            }
            """
        And that its "id" is "4"
        When I request "/roles"
        And the response has a "permissions" property
        And the "permissions.0" property equals "Manage Posts"
        Then the guzzle status code should be 200

     Scenario: Removing permissions from a role
        Given that I want to update a "Role"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {
                "permissions":[]
            }
            """
        And that its "id" is "4"
        When I request "/roles"
        And the response has a "permissions" property
        And the "permissions" property is empty
        Then the guzzle status code should be 200

     Scenario: Get role by name
        Given that I want to find a "Role"
        And that the oauth token is "testadminuser"
        And that the request "query string" is:
            """
            name=manager
            """
        When I request "/roles"
        Then the response is JSON
        And the "count" property equals "1"
        And the "results.0.name" property equals "manager"
        Then the guzzle status code should be 200

     Scenario: Get protected status of protected role
        Given that I want to find a "Role"
        And that the oauth token is "testadminuser"
        And that its "id" is "1"
        When I request "/roles"
        Then the response is JSON
		And the response has a "protected" property
		And the "protected" property is true
        Then the guzzle status code should be 200

     Scenario: Get protected status of unprotected role
        Given that I want to find a "Role"
        And that the oauth token is "testadminuser"
        And that its "id" is "4"
        When I request "/roles"
        Then the response is JSON
		And the response has a "protected" property
		And the "protected" property is false
        Then the guzzle status code should be 200

     Scenario: Delete a protected role
        Given that I want to delete a "Role"
        And that the oauth token is "testadminuser"
        And that its "id" is "1"
        When I request "/roles"
        Then the guzzle status code should be 403

     Scenario: Delete an unprotected role
        Given that I want to delete a "Role"
        And that the oauth token is "testadminuser"
        And that its "id" is "4"
        When I request "/roles"
        Then the guzzle status code should be 200

     Scenario: Change protected status of a role (Change should fail because "protected" is immutable)
        Given that I want to update a "Role"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {
                "protected": false
            }
            """
        And that its "id" is "1"
        When I request "/roles"
        Then the response is JSON
		And the response has a "protected" property
		And the "protected" property is true
        Then the guzzle status code should be 200
