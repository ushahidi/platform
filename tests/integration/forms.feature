@oauth2Skip
Feature: Testing the Forms API

    Scenario: Creating a new Form
        Given that I want to make a new "Form"
        And that the request "data" is:
            """
            {
                "name":"Test Form",
                "type":"report",
                "description":"This is a test form from BDD testing",
                "disabled":false
            }
            """
        When I request "/forms"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "disabled" property is false
        And the "require_approval" property is true
        And the "everyone_can_create" property is true
        And the response has a "everyone_can_create" property
        And the "everyone_can_create" property is true
        And the response has a "can_create" property
        And the "can_create" property is empty
        Then the guzzle status code should be 200

    Scenario: Updating a Form
        Given that I want to update a "Form"
        And that the request "data" is:
            """
            {
                "name":"Updated Test Form",
                "type":"report",
                "description":"This is a test form updated by BDD testing",
                "disabled":true,
                "require_approval":false,
                "everyone_can_create":false
            }
            """
        And that its "id" is "1"
        When I request "/forms"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the response has a "name" property
        And the "name" property equals "Updated Test Form"
        And the "disabled" property is true
        And the "require_approval" property is false
        And the "everyone_can_create" property is false
        Then the guzzle status code should be 200

    Scenario: Update a non-existent Form
        Given that I want to update a "Form"
        And that the request "data" is:
            """
            {
                "name":"Updated Test Form",
                "type":"report",
                "description":"This is a test form updated by BDD testing",
                "disabled":false
            }
            """
        And that its "id" is "40"
        When I request "/forms"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Listing All Forms
        Given that I want to get all "Forms"
        When I request "/forms"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a Form
        Given that I want to find a "Form"
        And that its "id" is "1"
        When I request "/forms"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Form
        Given that I want to find a "Form"
        And that its "id" is "35"
        When I request "/forms"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: POST method disabled for Form Roles
        Given that I want to make a new "FormRole"
        And that the request "data" is:
            """
            {
                "roles": [1]
            }
            """
        When I request "/forms/1/roles"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 405

    Scenario: DELETE method disabled for Form Roles
        Given that I want to delete a "FormRole"
        When I request "/forms/1/roles"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 405

    Scenario: Add 1 role to Form
        Given that I want to update a "FormRole"
        And that the request "data" is:
            """
            {
                "roles": [1]
            }
            """
        When I request "/forms/1/roles"
        Then the response is JSON
        And the response has a "count" property
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    Scenario: Add 2 roles to Form
        Given that I want to update a "FormRole"
        And that the request "data" is:
            """
            {
                "roles": [1,2]
            }
            """
        When I request "/forms/1/roles"
        Then the response is JSON
        And the response has a "count" property
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    Scenario: Finding a Form after roles have been set.
        Given that I want to find a "Form"
        And that its "id" is "1"
        When I request "/forms"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
		And the response has a "can_create" property
		And the response has a "can_create.0" property
		And the "can_create.0" property equals "user"
		And the response has a "can_create.1" property
		And the "can_create.1" property equals "admin"
        Then the guzzle status code should be 200

    Scenario: Remove roles from Form
        Given that I want to update a "FormRole"
        And that the request "data" is:
            """
            {
                "roles": []
            }
            """
        When I request "/forms/1/roles"
        Then the response is JSON
        And the response has a "count" property
        And the "count" property equals "0"
        Then the guzzle status code should be 200

    Scenario: Finding all Form Roles
        Given that I want to find a "FormRole"
        When I request "/forms/1/roles"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "2"
        Then the guzzle status code should be 200

    Scenario: Fail to add 1 invalid Role to Form
        Given that I want to update a "FormRole"
        And that the request "data" is:
            """
            {
                "roles": [120]
            }
            """
        When I request "/forms/1/roles"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    Scenario: Fail to add roles with 1 invalid Role id to Form
        Given that I want to update a "FormRole"
        And that the request "data" is:
            """
            {
                "roles": [1,2,120]
            }
            """
        When I request "/forms/1/roles"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    Scenario: Add roles to non-existent Form
        Given that I want to update a "FormRole"
        And that the request "data" is:
            """
            {
                "roles": [1]
            }
            """
        When I request "/forms/26/roles"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Delete a Form
        Given that I want to delete a "Form"
        And that its "id" is "1"
        When I request "/forms"
        Then the response is JSON
        And the response has a "id" property
        Then the guzzle status code should be 200

    Scenario: Fail to delete a non existent Form
        Given that I want to delete a "Form"
        And that its "id" is "35"
        When I request "/forms"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404
