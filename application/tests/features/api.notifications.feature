Feature: Testing the Notifications API

    Scenario: Subscribe to a notification
        Given that I want to make a new "Notification"
        And that the request "Authorization" header is "Bearer testbasicuser"
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
        And the "is_subscribed" property equals "1"
        Then the guzzle status code should be 200

    Scenario: Subscribing to an existing notification
        Given that I want to make a new "Notification"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that the request "data" is:
            """
            {
                "set":"2"
            }
            """
        When I request "/notifications"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "is_subscribed" property equals "1"
        Then the guzzle status code should be 200

    Scenario: An anonymous user cannot subscribe to a notification
        Given that I want to make a new "Notification"
        And that the request "Authorization" header is "Bearer testanon"
        And that the request "data" is:
            """
            {
                "set":"2"
            }
            """
        When I request "/notifications"
        Then the guzzle status code should be 400

    Scenario: Unsubscribe from a notification
        Given that I want to update a "Notification"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that the request "data" is:
            """
            {
                "set_id":"2",
                "is_subscribed":"0"
            }
            """
        And that its "id" is "2"
        When I request "/notifications"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "2"
        And the "is_subscribed" property equals "0"
        Then the guzzle status code should be 200

    Scenario: Subscribe to a previously unsubscribed notification
        Given that I want to update a "Notification"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that the request "data" is:
            """
            {
                "set_id":"3",
                "is_subscribed":"1"
            }
            """
        And that its "id" is "3"
        When I request "/notifications"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "3"
        And the "is_subscribed" property equals "1"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing Notifications for a user
        Given that I want to get all "Notifications"
        And that the request "Authorization" header is "Bearer testbasicuser"
        When I request "/notifications"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "4"
        Then the guzzle status code should be 200
