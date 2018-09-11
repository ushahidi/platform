@notifications
Feature: Testing the Notifications API

    Scenario: Subscribe to a notification
        Given that I want to make a new "Notification"
        And that the oauth token is "testbasicuser"
        And that the request "data" is:
            """
            {
                "set":"1"
            }
            """
        When I request "/notifications"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: An anonymous user cannot subscribe to a notification
        Given that I want to make a new "Notification"
        And that the oauth token is "testanon"
        And that the request "data" is:
            """
            {
                "set":"2"
            }
            """
        When I request "/notifications"
        Then the guzzle status code should be 403

    Scenario: Deleting a notification
        Given that I want to delete a "Notification"
        And that the oauth token is "testbasicuser"
        And that its "id" is "2"
        When I request "/notifications"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "2"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing Notifications for a user
        Given that I want to get all "Notifications"
        And that the oauth token is "testbasicuser"
        And that the request "query string" is:
            """
                user=me
            """
        When I request "/notifications"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "4"
        Then the guzzle status code should be 200
