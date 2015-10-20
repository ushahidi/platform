@oauth2Skip
Feature: Testing the Form Stages API

    Scenario: Creating a new Stage
        Given that I want to make a new "Stage"
        And that the request "data" is:
            """
            {
                "label":"First Stage",
                "priority": 1,
                "required": 1
            }
            """
        When I request "/forms/1/stages"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "label" property equals "First Stage"
        And the "priority" property equals "1"
        And the "required" property equals "1"
        Then the guzzle status code should be 200

    Scenario: Creating a new Stage with a non-existent Form
        Given that I want to make a new "Stage"
        And that the request "data" is:
            """
            {
                "label":"First Stage",
                "priority": 1
            }
            """
        When I request "/forms/35/stages"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Updating a Stage
        Given that I want to update a "Stage"
        And that the request "data" is:
            """
            {
                "label":"Dummy Stage Updated",
                "priority": 1
            }
            """
        And that its "id" is "1"
        When I request "/forms/1/stages"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the response has a "label" property
        And the "label" property equals "Dummy Stage Updated"
        Then the guzzle status code should be 200

    Scenario: Updating a non-existent Stage
        Given that I want to update a "Stage"
        And that the request "data" is:
            """
            {
                "label":"Missing Stage Updated",
                "priority": 1
            }
            """
        And that its "id" is "40"
        When I request "/forms/1/stages"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Listing All Stages for a form
        Given that I want to get all "Stages"
        When I request "/forms/1/stages"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Listing All Stages
        Given that I want to get all "Stages"
        When I request "/forms/stages"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a Stage
        Given that I want to find a "Stage"
        And that its "id" is "1"
        When I request "/forms/1/stages"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Stage
        Given that I want to find a "Stage"
        And that its "id" is "35"
        When I request "/forms/1/stages"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Stage
        Given that I want to delete a "Stage"
        And that its "id" is "1"
        When I request "/forms/1/stages"
        Then the guzzle status code should be 200

    Scenario: Deleting a non-existent Stage
        Given that I want to delete a "Stage"
        And that its "id" is "35"
        When I request "/forms/1/stages"
        And the response has a "errors" property
        Then the guzzle status code should be 404
