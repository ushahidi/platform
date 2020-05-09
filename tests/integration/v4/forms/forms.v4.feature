@oauth2Skip
Feature: Testing the Surveys API

    Scenario: Creating a new Survey
        Given that I want to make a new "Survey"
        And that the api_url is "api/v4"
        And that the request "data" is:
            """
            {
                "name":"Test Survey",
                "type":"report",
                "description":"This is a test form from BDD testing",
                "disabled":false
            }
            """
        When I request "/surveys"
        Then the response is JSON
        And the response has a "result" property
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the "result.disabled" property is false
        And the "result.require_approval" property is true
        And the "result.require_approval" property is true
        And the response has a "result.everyone_can_create" property
        And the "result.everyone_can_create" property is true
        And the response has a "result.can_create" property
        And the "result.can_create" property is empty
        Then the guzzle status code should be 201
#
#    Scenario: Updating a Survey
#        Given that I want to update a "Survey"
#        And that the api_url is "api/v4"
#        And that the request "data" is:
#            """
#            {
#                "name":"Updated Test Survey",
#                "type":"report",
#                "description":"This is a test form updated by BDD testing",
#                "disabled":true,
#                "require_approval":false,
#                "everyone_can_create":false,
#                "tags": [1,2,3,"junk"]
#            }
#            """
#        And that its "id" is "1"
#        When I request "/surveys"
#        Then the response is JSON
#        And the response has a "result.id" property
#        And the type of the "result.id" property is "numeric"
#        And the "result.id" property equals "1"
#        And the response has a "result.name" property
#        And the "result.name" property equals "Updated Test Survey"
#        And the "result.disabled" property is true
#        And the "result.require_approval" property is false
#        And the "result.everyone_can_create" property is false
#        Then the guzzle status code should be 200
#
#    Scenario: Updating a Survey to clear name should fail
#        Given that I want to update a "Survey"
#        And that the api_url is "api/v4"
#        And that the request "data" is:
#            """
#            {
#                "name":"",
#                "type":"report",
#                "description":"This is a test form updated by BDD testing",
#                "disabled":true,
#                "require_approval":false,
#                "everyone_can_create":false
#            }
#            """
#        And that its "id" is "1"
#        When I request "/surveys"
#        Then the response is JSON
#        Then the guzzle status code should be 422
#
#    Scenario: Update a non-existent Survey
#        Given that I want to update a "Survey"
#        And that the api_url is "api/v4"
#        And that the request "data" is:
#            """
#            {
#                "name":"Updated Test Survey",
#                "type":"report",
#                "description":"This is a test form updated by BDD testing",
#                "disabled":false
#            }
#            """
#        And that its "id" is "40"
#        When I request "/surveys"
#        Then the response is JSON
#        And the response has a "errors" property
#        Then the guzzle status code should be 404
#
    Scenario: Listing All Surveys
        Given that I want to get all "Surveys"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the "results" property count is "9"
        Then the guzzle status code should be 200

    Scenario: Finding a Survey
        Given that I want to find a "Survey"
        And that its "id" is "1"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Survey
        Given that I want to find a "Survey"
        And that the api_url is "api/v4"
        And that its "id" is "35"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404
##
##    Scenario: POST method disabled for Survey Roles
##        Given that I want to make a new "SurveyRole"
##        And that the api_url is "api/v4"
##        And that the request "data" is:
##            """
##            {
##                "roles": [1]
##            }
##            """
##        When I request "/surveys/1/roles"
##        Then the response is JSON
##        And the response has a "errors" property
##        Then the guzzle status code should be 405
##
##    Scenario: DELETE method disabled for Survey Roles
##        Given that I want to delete a "SurveyRole"
##        When I request "/surveys/1/roles"
##        Then the response is JSON
##        And the response has a "errors" property
##        Then the guzzle status code should be 405
##
##    Scenario: Add 1 role to Survey
##        Given that I want to update a "SurveyRole"
##        And that the request "data" is:
##            """
##            {
##                "roles": [1]
##            }
##            """
##        When I request "/surveys/1/roles"
##        Then the response is JSON
##        And the response has a "count" property
##        And the "count" property equals "1"
##        Then the guzzle status code should be 200
##
##    Scenario: Add 2 roles to Survey
##        Given that I want to update a "SurveyRole"
##        And that the request "data" is:
##            """
##            {
##                "roles": [1,2]
##            }
##            """
##        When I request "/surveys/1/roles"
##        Then the response is JSON
##        And the response has a "count" property
##        And the "count" property equals "2"
##        Then the guzzle status code should be 200
##
##    Scenario: Finding a Survey after roles have been set.
##        Given that I want to update a "SurveyRole"
##        And that the request "data" is:
##            """
##            {
##                "roles": [1,2]
##            }
##            """
##        When I request "/surveys/1/roles"
##        Then the response is JSON
##        Given that I want to find a "Survey"
##        And that its "id" is "1"
##        When I request "/surveys"
##        Then the response is JSON
##        And the response has a "id" property
##        And the type of the "id" property is "numeric"
##		And the response has a "can_create" property
##		And the response has a "can_create.0" property
##		And the "can_create.0" property equals "user"
##		And the response has a "can_create.1" property
##		And the "can_create.1" property equals "admin"
##        Then the guzzle status code should be 200
##
##    Scenario: Remove roles from Survey
##        Given that I want to update a "SurveyRole"
##        And that the request "data" is:
##            """
##            {
##                "roles": []
##            }
##            """
##        When I request "/surveys/1/roles"
##        Then the response is JSON
##        And the response has a "count" property
##        And the "count" property equals "0"
##        Then the guzzle status code should be 200
##
##    Scenario: Finding all Survey Roles
##        Given that I want to find a "SurveyRole"
##        When I request "/surveys/1/roles"
##        Then the response is JSON
##        And the response has a "count" property
##        And the type of the "count" property is "2"
##        Then the guzzle status code should be 200
##
##    Scenario: Fail to add 1 invalid Role to Survey
##        Given that I want to update a "SurveyRole"
##        And that the request "data" is:
##            """
##            {
##                "roles": [120]
##            }
##            """
##        When I request "/surveys/1/roles"
##        Then the response is JSON
##        And the response has a "errors" property
##        Then the guzzle status code should be 422
##
##    Scenario: Fail to add roles with 1 invalid Role id to Survey
##        Given that I want to update a "SurveyRole"
##        And that the request "data" is:
##            """
##            {
##                "roles": [1,2,120]
##            }
##            """
##        When I request "/surveys/1/roles"
##        Then the response is JSON
##        And the response has a "errors" property
##        Then the guzzle status code should be 422
##
##    Scenario: Add roles to non-existent Survey
##        Given that I want to update a "SurveyRole"
##        And that the request "data" is:
##            """
##            {
##                "roles": [1]
##            }
##            """
##        When I request "/surveys/26/roles"
##        Then the response is JSON
##        And the response has a "errors" property
##        Then the guzzle status code should be 404
#
#    Scenario: Delete a Survey
#        Given that I want to delete a "Survey"
#        And that the api_url is "api/v4"
#        And that its "id" is "1"
#        When I request "/surveys"
#        Then the response is JSON
#        And the response has a "id" property
#        Then the guzzle status code should be 200
#
#    Scenario: Fail to delete a non existent Survey
#        Given that I want to delete a "Survey"
#        And that the api_url is "api/v4"
#        And that its "id" is "35"
#        When I request "/surveys"
#        Then the response is JSON
#        And the response has a "errors" property
#        Then the guzzle status code should be 404
