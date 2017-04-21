@migrations
Feature: Testing the Migration API

    Scenario: Run migration
        Given that I want to make a new "Migration"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {}
            """
        When I request "/migration/migrate"
        Then the response is JSON
        Then the guzzle status code should be 200

    Scenario: Run migration
        Given that I want to make a new "Rollback"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {}
            """
        When I request "/migration/rollback"
        Then the response is JSON
        Then the guzzle status code should be 200

    Scenario: Get migration status
        Given that I want to get all "Status"
        And that the oauth token is "testadminuser"
        When I request "/migration"
        Then the response is JSON
        Then the guzzle status code should be 200

    Scenario: Get migration status
        Given that I want to get all "Status"
        And that the oauth token is "testadminuser"
        When I request "/migration/status"
        Then the response is JSON
        Then the guzzle status code should be 200

    Scenario: Run migration
        Given that I want to find a "Migration"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {}
            """
        And that the api_url is ""
        When I request "/migrate"
        Then the response is JSON
        Then the guzzle status code should be 200
