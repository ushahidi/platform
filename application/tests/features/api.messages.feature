@oauth2Skip
Feature: Testing the Messages API

    Scenario: Creating a new Message
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            {
                "message":"Test creating outgoing",
                "type":"sms",
                "direction":"outgoing",
                "contact_id":"1"
            }
            """
        When I request "/messages"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "message" property equals "Test creating outgoing"
        And the "status" property equals "pending"
        Then the guzzle status code should be 200

    Scenario: Creating an incoming message should fail
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            {
                "message":"Test creating outgoing",
                "type":"sms",
                "direction":"incoming",
                "contact_id":"1"
            }
            """
        When I request "/messages"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 400

    Scenario: Updating an incoming message ignores changes
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

    Scenario: Updating an incoming message to "sent" fails
        Given that I want to update a "Message"
        And that the request "data" is:
            """
            {
                "message": "Overwrite message",
                "status": "sent"
            }
            """
        And that its "id" is "1"
        When I request "/messages"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 400

    Scenario: Updating an outgoing message should only update status
        Given that I want to update a "Message"
        And that the request "data" is:
            """
            {
                "message": "Updated message",
                "status": "cancelled"
            }
            """
        And that its "id" is "7"
        When I request "/messages"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "7"
        And the "message" property equals "Updated message"
        And the "status" property equals "cancelled"
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
    Scenario: Listing Messages (default filters)
        Given that I want to get all "Messages"
        When I request "/messages"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "7"
        Then the guzzle status code should be 200

    Scenario: Listing All Messages in the inbox
        Given that I want to get all "Messages"
        And that the request "query string" is:
            """
            box=inbox
            """
        When I request "/messages"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "5"
        Then the guzzle status code should be 200

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

    Scenario: Search All Messages by direction
        Given that I want to get all "Messages"
        And that the request "query string" is:
            """
            box=outbox
            """
        When I request "/messages"
        Then the response is JSON
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    Scenario: Search All Messages by status
        Given that I want to get all "Messages"
        And that the request "query string" is:
            """
            status=pending&box=outbox
            """
        When I request "/messages"
        Then the response is JSON
        And the "count" property equals "1"
        And the "results.0.id" property equals "7"
        Then the guzzle status code should be 200

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

    @resetFixture @post
    Scenario: Creating a new Post from a Message
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form": 2
            }
            """
        When I request "/messages/1/post"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "title" property
        And the "title" property equals "abc"
        And the response has a "content" property
        And the "content" property equals "A test message"
        And the "form.id" property equals "2"
        Then the guzzle status code should be 200

    Scenario: Finding a Post from a Message
        Given that I want to find a "Post"
        When I request "/messages/1/post"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "title" property
        And the "title" property equals "abc"
        And the response has a "content" property
        And the "content" property equals "A test message"
        And the "form.id" property equals "2"
        Then the guzzle status code should be 200
