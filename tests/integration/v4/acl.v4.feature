@acl
Feature: V4 API Access Control Layer
    Scenario: Listing All Stages for all forms with hidden tasks
        Given that I want to get all "Surveys"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "results.7.tasks" property
        And the "results.7.tasks" property count is "1"
        And the "results.7.tasks.0.fields" property count is "0"
        Then the guzzle status code should be 200
    Scenario: Listing All fields for a stage in an array of forms with a multi-stage form
        Given that I want to get all "Surveys"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "results.0.tasks" property
        And the "results.0.tasks" property count is "3"
        And the "results.0.tasks.0.fields" property count is "17"
        Then the guzzle status code should be 200
    Scenario: Listing All Stages for a form in an array of forms with hidden tasks as admin
        Given that I want to get all "Surveys"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "results.7.tasks" property
        And the "results.7.tasks" property count is "4"
        And the "results.7.tasks.0.fields" property count is "2"
    Scenario: Listing All Stages for a form with hidden tasks as admin
        Given that I want to find a "Survey"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        When I request "/surveys/8"
        Then the response is JSON
        And the response has a "result.tasks" property
        And the "result.tasks" property count is "4"
        And the "result.tasks.0.fields" property count is "2"
        And the "result.tasks.2.fields" property count is "0"
    Scenario: Listing All Stages for a form with hidden tasks as a normal user
        Given that I want to find a "Survey"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        When I request "/surveys/8"
        Then the response is JSON
        And the response has a "result.tasks" property
        And the "result.tasks" property count is "1"
        And the "result.tasks.0.fields" property count is "0"
    @rolesEnabled
    Scenario: User with Manage Settings permission can create a hydrated form
        Given that I want to make a new "Survey"
        And that the oauth token is "testmanager"
        And that the api_url is "api/v4"
        And that the request "data" is:
            """
            {
                "enabled_languages": {
                    "default": "en-EN"
                },
                "color": null,
                "require_approval": true,
                "everyone_can_create": false,
                "targeted_survey": false,
                "tasks": [
                    {
                        "label": "Post",
                        "priority": 0,
                        "required": false,
                        "type": "post",
                        "show_when_published": true,
                        "task_is_internal_only": false,
                        "fields": [
                            {
                                "cardinality": 0,
                                "input": "text",
                                "label": "Title",
                                "priority": 1,
                                "required": true,
                                "type": "title",
                                "options": [],
                                "config": {}
                            },
                            {
                                "cardinality": 0,
                                "input": "text",
                                "label": "Description",
                                "priority": 2,
                                "required": true,
                                "type": "description",
                                "options": [],
                                "config": {}
                            }
                        ],
                        "is_public": true
                    }
                ],
                "name": "new"
            }
            """
        When I request "/surveys"
        Then the response is JSON
        And the response has a "result" property
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the response has a "result.tasks" property
        And the type of the "result.tasks" property is "array"
        And the response has a "result.tasks.0.fields" property
        And the "result.tasks.0.fields" property count is "2"
        And the "result.name" property equals "new"
        Then the guzzle status code should be 200
    @rolesEnabled
    Scenario: Basic user CANNOT create a hydrated form
        Given that I want to make a new "Survey"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        And that the request "data" is:
            """
            {
                "enabled_languages": {
                    "default": "en-EN"
                },
                "color": null,
                "require_approval": true,
                "everyone_can_create": false,
                "targeted_survey": false,
                "tasks": [
                    {
                        "label": "Post",
                        "priority": 0,
                        "required": false,
                        "type": "post",
                        "show_when_published": true,
                        "task_is_internal_only": false,
                        "fields": [
                            {
                                "cardinality": 0,
                                "input": "text",
                                "label": "Title",
                                "priority": 1,
                                "required": true,
                                "type": "title",
                                "options": [],
                                "config": {}
                            },
                            {
                                "cardinality": 0,
                                "input": "text",
                                "label": "Description",
                                "priority": 2,
                                "required": true,
                                "type": "description",
                                "options": [],
                                "config": {}
                            }
                        ],
                        "is_public": true
                    }
                ],
                "name": "new"
            }
            """
        When I request "/surveys"
        Then the guzzle status code should be 403
