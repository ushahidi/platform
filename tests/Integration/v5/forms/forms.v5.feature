@rolesEnabled
Feature: Testing the Surveys API
    Scenario: Creating a new Survey
        Given that I want to make a new "Survey"
        And that the oauth token is "testmanager"
        And that the api_url is "api/v5"
        And that the request "data" is:
            """
            {
                "name":"Test Survey",
                "type":"report",
                "description":"This is a test form from BDD testing",
                "disabled":false,
                "color": "#A51A1A"
            }
            """
        When I request "/surveys"
        Then the response is JSON
        And the response has a "result" property
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the "result.color" property equals "#A51A1A"
        And the "result.disabled" property is false
        And the "result.require_approval" property is true
        And the "result.require_approval" property is true
        And the response has a "result.everyone_can_create" property
        And the "result.everyone_can_create" property is true
        And the response has a "result.can_create" property
        And the "result.can_create" property is empty
        Then the guzzle status code should be 201
    Scenario: A tag field with a response_private true value should fail
        Given that I want to make a new "Survey"
        And that the oauth token is "testmanager"
        And that the api_url is "api/v5"
        And that the request "data" is:
            """
            {
                "name":"Test Survey",
                "type":"report",
                "description":"This is a test form from BDD testing - 2",
                "disabled":false,
                "color": "#A51A1A",
                "tasks": [
                    {
                    "label": "It will fail",
                    "priority": 0,
                    "required": 0,
                    "type": "post",
                    "description": null,
                    "show_when_published": 1,
                    "task_is_internal_only": 0,
                    "fields": [
                        {
                            "id": 26,
                            "key": "tags1",
                            "label": "Categories",
                            "instructions": null,
                            "input": "tags",
                            "type": "tags",
                            "required": 0,
                            "default": null,
                            "priority": 3,
                            "options": [
                                "1",
                                "2",
                                "3",
                                "4",
                                "5",
                                "6",
                                "7"
                            ],
                            "cardinality": 0,
                            "config": null,
                            "response_private": 1,
                            "form_stage_id": 1,
                            "translations": {
                                "es": {
                                    "label": "ES Categories",
                                    "options": [
                                        "ES 1",
                                        "ES 2",
                                        "ES 3",
                                        "ES 4",
                                        "ES 5",
                                        "ES 6",
                                        "ES 7"
                                    ]
                                }
                            }
                        }
                    ]
                }
                ]
            }
            """
        When I request "/surveys"
        Then the response is JSON
        And the response has a "tasks.0.fields.0.response_private" property
        And the "tasks.0.fields.0.response_private" property contains "Tag fields cannot be private."
        Then the guzzle status code should be 422

    Scenario: Updating a Survey to clear name should fail
        Given that I want to update a "Survey"
        And that the api_url is "api/v5"
        And that the oauth token is "testmanager"
        And that the request "data" is:
            """
            {
                "name":"",
                "type":"report",
                "description":"This is a test form updated by BDD testing",
                "disabled":true,
                "require_approval":false,
                "everyone_can_create":false
            }
            """
        And that its "id" is "1"
        When I request "/surveys"
        Then the response is JSON
        Then the guzzle status code should be 422

    Scenario: Update a non-existent Survey
        Given that I want to update a "Survey"
        And that the api_url is "api/v5"
        And that the oauth token is "testmanager"
        And that the request "data" is:
            """
            {
                "name":"Updated Test Survey",
                "type":"report",
                "description":"This is a test form updated by BDD testing",
                "disabled":false
            }
            """
        And that its "id" is "440"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "error" property
        Then the guzzle status code should be 404

    Scenario: Listing All Surveys
        Given that I want to get all "Surveys"
        And that the api_url is "api/v5"
        And that the oauth token is "testmanager"
        When I request "/surveys"
        Then the response is JSON
        And the "results" property count is "11"
        Then the guzzle status code should be 200

    Scenario: Finding a Survey
        Given that I want to find a "Survey"
        And that its "id" is "1"
        And that the api_url is "api/v5"
        And that the oauth token is "testmanager"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Deleting a Survey
        Given that I want to delete a "Survey"
        And that its "id" is "1"
        And that the api_url is "api/v5"
        And that the oauth token is "testmanager"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "result.deleted" property
        And the type of the "result.deleted" property is "numeric"
        And the "result.deleted" property equals "1"
        Then the guzzle status code should be 200
    Scenario: Finding a non-existent Survey
        Given that I want to find a "Survey"
        And that the api_url is "api/v5"
        And that the oauth token is "testmanager"
        And that its "id" is "1"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "error" property
        Then the guzzle status code should be 404
