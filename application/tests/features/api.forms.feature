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
                                "key":"full_name",
                                "label":"Full Name",
                                "type":"varchar",
                                "input":"text",
                                "required":true,
                                "priority":1
                            },
                            {
                                "key":"last_name",
                                "label":"Last Name",
                                "type":"varchar",
                                "input":"text",
                                "required":false,
                                "priority":2
                            },
                            {
                                "key":"missing_status",
                                "label":"Status",
                                "type":"varchar",
                                "input":"text",
                                "required":false,
                                "priority":2,
                                "options":[
                                    "Missing",
                                    "Alive",
                                    "Dead"
                                ]
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
        Then the response status code should be 200

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
        Then the response status code should be 200

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
        Then the response status code should be 404

    Scenario: Listing All Forms
        Given that I want to get all "Forms"
        When I request "/forms"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the response status code should be 200

    Scenario: Finding a Form
        Given that I want to find a "Form"
        And that its "id" is "1"
        When I request "/forms"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the response status code should be 200

    Scenario: Finding a non-existent Form
        Given that I want to find a "Form"
        And that its "id" is "35"
        When I request "/forms"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 404

    Scenario: Deleting a Form
        Given that I want to delete a "Form"
        And that its "id" is "1"
        When I request "/forms"
        Then the response is JSON
        And the response has a "id" property
        Then the response status code should be 200

    Scenario: Fail to delete a non existent Form
        Given that I want to delete a "Form"
        And that its "id" is "35"
        When I request "/forms"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 404
