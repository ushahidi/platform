@oauth2Skip
Feature: Testing the Sets Posts API

    Scenario: Creating a new Post in a Set
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "id":1
            }
            """
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Listing All Posts in a Set
        Given that I want to get all "Attributes"
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a Post in a Set
        Given that I want to find a "Post"
        And that its "id" is "1"
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200