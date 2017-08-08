@postlock @oauth2Skip
Feature: Testing Post Lock

    Scenario: Check Post Lock
        Given that I want to check a "PostLock"
        When I request "/posts/1690/lock"
        Then the response is JSON
        And the response has a "post_locked" property
        And the "post_locked" property is false
        Then the guzzle status code should be 200

    Scenario: Get Post Lock
        Given that I want to make a new "PostLock"
         And that the request "data" is:
            """
            {
            }
            """
        When I request "/posts/1691/lock"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Check New Post Lock
        Given that I want to check a "PostLock"
        When I request "/posts/1691/lock"
        Then the response is JSON
        And the response has a "post_locked" property
        And the "post_locked" property equals "true"
        Then the guzzle status code should be 200

    Scenario: Break a lock for a given post
        Given that I want to update a "PostLock"
        And that the request "data" is:
            """
            {
            }
            """
        When I request "/posts/1691/lock"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200


   