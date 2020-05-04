@acl
Feature: V4 API Access Control Layer
    Scenario: Listing All Stages for a form with hidden stages
        Given that I want to get all "Surveys"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "results.7.stages" property
        And the "results.7.stages" property count is "1"
        And the "results.7.stages.0.attributes" property count is "0"
        Then the guzzle status code should be 200
    Scenario: Listing All attributes for a stage in a multi-stage form
        Given that I want to get all "Surveys"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "results.0.stages" property
        And the "results.0.stages" property count is "3"
        And the "results.0.stages.0.attributes" property count is "17"
        Then the guzzle status code should be 200
    Scenario: Listing All Stages and attributes for a form with hidden stages as admin
        Given that I want to get all "Surveys"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        When I request "/surveys"
        Then the response is JSON
        And the response has a "results.7.stages" property
        And the "results.7.stages" property count is "4"
        And the "results.7.stages.0.attributes" property count is "2"
