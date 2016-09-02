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
        Then the guzzle status code should be 200

    Scenario: Updating a Form
        Given that I want to update a "Form"
        And that the request "data" is:
            """
            {
                "name":"Updated Test Form",
                "type":"report",
                "description":"This is a test form updated by BDD testing",
                "disabled":true
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

    Scenario: Deleting a Form
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
