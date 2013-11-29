@oauth2Skip
Feature: Testing the Messages API

    Scenario: Creating a new Message
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            {
                "message":"Boxes"
            }
            """
        When I request "/messages"
        Then the guzzle status code should be 405

    Scenario: Updating a Message
        Given that I want to update a "Message"
        And that the request "data" is:
            """
            {
                "message": "Overwrite message",
                "status": "archived"
            }
            """
        And that its "id" is "1"
        When I request "/messages"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the "message" property equals "A test message"
        And the "status" property equals "archived"
        Then the guzzle status code should be 200

    Scenario: Updating a non-existent Message
        Given that I want to update a "Message"
        And that the request "data" is:
            """
            {
                "message": "Overwrite message",
                "status": "archived"
            }
            """
        And that its "id" is "40"
        When I request "/messages"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    @resetFixture
    Scenario: Listing All Messages
        Given that I want to get all "Messages"
        When I request "/messages"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "7"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Messages
        Given that I want to get all "Messages"
        And that the request "query string" is:
            """
            q=abc
            """
        When I request "/messages"
        Then the response is JSON
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Messages by type
        Given that I want to get all "Messages"
        And that the request "query string" is:
            """
            type=email
            """
        When I request "/messages"
        Then the response is JSON
        And the "count" property equals "1"
        And the "results.0.id" property equals "3"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Messages by direction
        Given that I want to get all "Messages"
        And that the request "query string" is:
            """
            direction=outgoing
            """
        When I request "/messages"
        Then the response is JSON
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Messages by status
        Given that I want to get all "Messages"
        And that the request "query string" is:
            """
            status=pending
            """
        When I request "/messages"
        Then the response is JSON
        And the "count" property equals "1"
        And the "results.0.id" property equals "7"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Get all messages regardless of status
        Given that I want to get all "Messages"
        And that the request "query string" is:
            """
            status=all
            """
        When I request "/messages"
        Then the response is JSON
        And the "count" property equals "8"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Messages by provider
        Given that I want to get all "Messages"
        And that the request "query string" is:
            """
            data_provider=smssync
            """
        When I request "/messages"
        Then the response is JSON
        And the "count" property equals "1"
        And the "results.0.id" property equals "5"
        Then the guzzle status code should be 200

    Scenario: Finding a Message
        Given that I want to find a "Message"
        And that its "id" is "1"
        When I request "/messages"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Message
        Given that I want to find a "Message"
        And that its "id" is "35"
        When I request "/messages"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Message fails with 405
        Given that I want to delete a "Message"
        And that its "id" is "1"
        When I request "/messages"
        Then the guzzle status code should be 405

    Scenario: Deleting a non-existent Message fails with 405
        Given that I want to delete a "Message"
        And that its "id" is "35"
        When I request "/messages"
        Then the guzzle status code should be 405
