@oauth2Skip
Feature: Testing the Sets Posts API

    Scenario: Adding a new Post to a Set
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "id":95
            }
            """
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "95"
        Then the guzzle status code should be 200

    Scenario: Adding a non-existent Post in a Set fails
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "id":55
            }
            """
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 400

    @resetFixture
    Scenario: Listing All Posts in a Set
        Given that I want to get all "Posts"
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "3"
        Then the guzzle status code should be 200

    Scenario: Finding a Post in a Set
        Given that I want to find a "Post"
        And that its "id" is "9999"
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "9999"
        Then the guzzle status code should be 200

    Scenario: Finding a Post thats not in a Set should fail
        Given that I want to find a "Post"
        And that its "id" is "110"
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Remove a Post from a Set
        Given that I want to delete a "Post"
        And that its "id" is "9999"
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "9999"
        Then the guzzle status code should be 200

    Scenario: Remove a Post from a Set
        Given that I want to delete a "Post"
        And that its "id" is "110"
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404