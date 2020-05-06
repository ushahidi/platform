@acl
Feature: V4 API Access Control Layer
    Scenario: Listing All Stages for all forms with hidden stages
        Given that I want to get all "Surveys"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "results.7.stages" property
        And the "results.7.stages" property count is "1"
        And the "results.7.stages.0.attributes" property count is "0"
        Then the guzzle status code should be 200
    Scenario: Listing All attributes for a stage in an array of forms with a multi-stage form
        Given that I want to get all "Surveys"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "results.0.stages" property
        And the "results.0.stages" property count is "3"
        And the "results.0.stages.0.attributes" property count is "17"
        Then the guzzle status code should be 200
    Scenario: Listing All Stages for a form in an array of forms with hidden stages as admin
        Given that I want to get all "Surveys"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "results.7.stages" property
        And the "results.7.stages" property count is "4"
        And the "results.7.stages.0.attributes" property count is "2"
    Scenario: Listing All Stages for a form with hidden stages as admin
        Given that I want to find a "Survey"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        When I request "/surveys/8"
        Then the response is JSON
        And the response has a "result.stages" property
        And the "result.stages" property count is "4"
        And the "result.stages.0.attributes" property count is "2"
        And the "result.stages.2.attributes" property count is "0"
    Scenario: Listing All Stages for a form with hidden stages as a normal user
        Given that I want to find a "Survey"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        When I request "/surveys/8"
        Then the response is JSON
        And the response has a "result.stages" property
        And the "result.stages" property count is "1"
        And the "result.stages.0.attributes" property count is "0"
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
                "stages": [
                    {
                        "label": "Post",
                        "priority": 0,
                        "required": false,
                        "type": "post",
                        "show_when_published": true,
                        "task_is_internal_only": false,
                        "attributes": [
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
        And the response has a "result.stages" property
        And the type of the "result.stages" property is "array"
        And the response has a "result.stages.0.attributes" property
        And the "result.stages.0.attributes" property count is "2"
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
                "stages": [
                    {
                        "label": "Post",
                        "priority": 0,
                        "required": false,
                        "type": "post",
                        "show_when_published": true,
                        "task_is_internal_only": false,
                        "attributes": [
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
