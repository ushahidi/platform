@oauth2Skip @forms @form_attributes
Feature: Testing the Form Attributes API

    Scenario: Creating a new Attribute
        Given that I want to make a new "Attribute"
        And that the request "data" is:
            """
            {
                "form_group":1,
                "key":"new",
                "label":"Full Name",
                "type":"varchar",
                "input":"text",
                "required":true,
                "priority":1,
                "default":"",
                "options":{}
            }
            """
        When I request "/forms/1/attributes"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Creating a new Attribute on a non-existent Group
        Given that I want to make a new "Attribute"
        And that the request "data" is:
            """
            {
                "form_group":35,
                "key":"new",
                "label":"Full Name",
                "type":"varchar",
                "input":"text",
                "required":true,
                "priority":1
            }
            """
        When I request "/forms/1/attributes"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 400

    Scenario: Creating a new Attribute on a Group with the wrong Form ID
        Given that I want to make a new "Attribute"
        And that the request "data" is:
            """
            {
                "form_group":1,
                "key":"some_key",
                "label":"Hey a Thing",
                "type":"varchar",
                "input":"text",
                "required":true,
                "priority":1
            }
            """
        When I request "/forms/2/attributes"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 400

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
        When I request "/forms/1/attributes"
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

    Scenario: Updating a Attribute
        Given that I want to update a "Attribute"
        And that the request "data" is:
            """
            {
                "key":"updated",
                "label":"Full Name Updated",
                "type":"varchar",
                "input":"text",
                "required":true,
                "priority":1
            }
            """
        And that its "id" is "1"
        When I request "/forms/1/attributes"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the response has a "label" property
        And the "label" property equals "Full Name Updated"
        Then the guzzle status code should be 200

    Scenario: Updating a non-existent Attribute
        Given that I want to update a "Attribute"
        And that the request "data" is:
            """
            {
                "key":"updated",
                "label":"Full Name Updated",
                "type":"varchar",
                "input":"text",
                "required":true,
                "priority":1
            }
            """
        And that its "id" is "40"
        When I request "/forms/1/attributes"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Listing All Attributes
        Given that I want to get all "Attributes"
        When I request "/forms/1/attributes"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a Attribute
        Given that I want to find a "Attribute"
        And that its "id" is "1"
        When I request "/forms/1/attributes"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Attribute
        Given that I want to find a "Attribute"
        And that its "id" is "35"
        When I request "/forms/1/attributes"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Attribute
        Given that I want to delete a "Attribute"
        And that its "id" is "1"
        When I request "/forms/1/attributes"
        Then the guzzle status code should be 200

    Scenario: Deleting a non-existent Attribute
        Given that I want to delete a "Attribute"
        And that its "id" is "35"
        When I request "/forms/1/attributes"
        And the response has a "errors" property
        Then the guzzle status code should be 404
