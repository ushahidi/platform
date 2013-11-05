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
                "groups":[
                    {
                        "label":"First Group",
                        "priority": 1,
                        "attributes":[
                            {
                                "key":"test_full_name",
                                "label":"Full Name",
                                "type":"varchar",
                                "input":"text",
                                "required":true,
                                "priority":1,
                                "default":"",
                                "options":{}
                            },
                            {
                                "key":"test_last_name",
                                "label":"Last Name",
                                "type":"varchar",
                                "input":"text",
                                "required":false,
                                "priority":11
                            },
                            {
                                "key":"test_missing_status",
                                "label":"Status",
                                "type":"varchar",
                                "input":"text",
                                "required":false,
                                "priority":2,
                                "default":"Missing",
                                "options":[
                                    "Missing",
                                    "Alive",
                                    "Dead"
                                ]
                            },
                            {
                                "id":5
                            }
                        ]
                    }
                ]
            }
            """
        When I request "/forms"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "groups.0.attributes.0.key" property equals "date_of_birth"
        And the "groups.0.attributes.2.priority" property equals "11"
        And the "groups.0.attributes.3.default" property equals "Missing"
        And the "groups.0.attributes.3.options.1" property equals "Alive"
        Then the guzzle status code should be 200

    Scenario: Updating a Form
        Given that I want to update a "Form"
        And that the request "data" is:
            """
            {
                "name":"Updated Test Form",
                "type":"report",
                "description":"This is a test form updated by BDD testing"
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
        Then the guzzle status code should be 200

    Scenario: Update a non-existent Form
        Given that I want to update a "Form"
        And that the request "data" is:
            """
            {
                "name":"Updated Test Form",
                "type":"report",
                "description":"This is a test form updated by BDD testing"
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
