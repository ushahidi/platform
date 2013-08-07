@oauth2Skip
Feature: Testing the Form Groups API

    Scenario: Creating a new Group
        Given that I want to make a new "Group"
        And that the request "data" is:
            """
            {
                "label":"First Group",
                "priority": 1
            }
            """
        When I request "/forms/1/groups"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Creating a new Group with a non-existent Form
        Given that I want to make a new "Group"
        And that the request "data" is:
            """
            {
                "label":"First Group",
                "priority": 1
            }
            """
        When I request "/forms/35/groups"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Updating a Group
        Given that I want to update a "Group"
        And that the request "data" is:
            """
            {
                "label":"Dummy Group Updated",
                "priority": 1
            }
            """
        And that its "id" is "1"
        When I request "/forms/1/groups"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the response has a "label" property
        And the "label" property equals "Dummy Group Updated"
        Then the guzzle status code should be 200

    Scenario: Updating a non-existent Group
        Given that I want to update a "Group"
        And that the request "data" is:
            """
            {
                "label":"Missing Group Updated",
                "priority": 1
            }
            """
        And that its "id" is "40"
        When I request "/forms/1/groups"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Listing All Groups
        Given that I want to get all "Groups"
        When I request "/forms/1/groups"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a Group
        Given that I want to find a "Group"
        And that its "id" is "1"
        When I request "/forms/1/groups"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Group
        Given that I want to find a "Group"
        And that its "id" is "35"
        When I request "/forms/1/groups"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Group
        Given that I want to delete a "Group"
        And that its "id" is "1"
        When I request "/forms/1/groups"
        Then the guzzle status code should be 200

    Scenario: Deleting a non-existent Group
        Given that I want to delete a "Group"
        And that its "id" is "35"
        When I request "/forms/1/groups"
        And the response has a "errors" property
        Then the guzzle status code should be 404
