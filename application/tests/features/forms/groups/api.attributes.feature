@oauth2Skip
Feature: Testing the Form Groups API

    Scenario: Creating a new Attribute in a Group
        Given that I want to make a new "Attribute"
        And that the request "data" is:
            """
            {
                "key":"new_group_attr",
                "label":"Full Name",
                "type":"varchar",
                "input":"text",
                "required":true,
                "priority":1,
                "default":"",
                "options":{}
            }
            """
        When I request "/forms/1/groups/1/attributes"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Creating a new Attribute in a non-existent Group
        Given that I want to make a new "Attribute"
        And that the request "data" is:
            """
            {
                "key":"new_group_attr",
                "label":"Full Name",
                "type":"varchar",
                "input":"text",
                "required":true,
                "priority":1
            }
            """
        When I request "/forms/1/groups/35/attributes"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Adding an existing Attribute to a Group
        Given that I want to make a new "Attribute"
        And that the request "data" is:
            """
            {
                "id":1
            }
            """
        When I request "/forms/1/groups/1/attributes"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Check all attribute values were saved
        Given that I want to make a new "Attribute"
        And that the request "data" is:
            """
            {
                "form_group":1,
                "key":"value_test",
                "label":"Value test",
                "type":"varchar",
                "input":"text",
                "required":true,
                "priority":11,
                "default":"default val",
                "options":[
                  "option1",
                  "option2"
                ]
            }
            """
        When I request "/forms/1/groups/1/attributes"
        Then the response is JSON
        And the "key" property equals "value_test"
        And the "label" property equals "Value test"
        And the "type" property equals "varchar"
        And the "input" property equals "text"
        And the "required" property equals "true"
        And the "priority" property equals "11"
        And the "default" property equals "default val"
        And the "options.0" property equals "option1"
        Then the guzzle status code should be 200

    Scenario: Listing All Attributes in a Group
        Given that I want to get all "Attributes"
        When I request "/forms/1/groups/1/attributes"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding an Attribute in a Group
        Given that I want to find a "Attribute"
        And that its "id" is "1"
        When I request "/forms/1/groups/1/attributes"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Remove an Attribute from a Group
        Given that I want to delete a "Attribute"
        And that its "id" is "1"
        When I request "/forms/1/groups/1/attributes/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200
