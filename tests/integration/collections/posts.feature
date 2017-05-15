@oauth2Skip
Feature: Testing the Sets Posts API

    Scenario: Adding a new Post to a Collection
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "id":95
            }
            """
        When I request "/collections/1/posts/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "95"
        Then the guzzle status code should be 200

    Scenario: Add nonexistent post to collection fails
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "id":75
            }
            """
        When I request "/collections/1/posts/"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    @resetFixture
    Scenario: Listing All Posts in a Collection
        Given that I want to get all "Posts"
        When I request "/collections/1/posts/"
        Then the response is JSON
        And the "count" property equals "3"
        Then the guzzle status code should be 200

    Scenario: Listing posts for a non-existent collection should 404
        Given that I want to get all "Posts"
        When I request "/collections/22/posts"
        Then the response is JSON
        Then the guzzle status code should be 404

    Scenario: Finding a Post in a Set
        Given that I want to find a "Post"
        And that its "id" is "9999"
        When I request "/collections/1/posts/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "9999"
        Then the guzzle status code should be 200

    Scenario: Finding a Post thats not in a Set should fail
        Given that I want to find a "Post"
        And that its "id" is "110"
        When I request "/collections/1/posts/"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Remove a Post from a Collection
        Given that I want to delete a "Post"
        And that its "id" is "9999"
        When I request "/collections/1/posts/"
        Then the response is JSON
        And the "id" property equals "9999"
        Then the guzzle status code should be 200

# ACL Tests
    Scenario: Adding a post we cannot access to a collection fails
        Given that I want to make a new "Post"
        And that the oauth token is "testbasicuser2"
        And that the request "data" is:
            """
            {
                "id":111
            }
            """
        When I request "/collections/1/posts/"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 403

    Scenario: Adding a post to a collection we cannot access fails
        Given that I want to make a new "Post"
        And that the oauth token is "testbasicuser2"
        And that the request "data" is:
            """
            {
                "id":97
            }
            """
        When I request "/collections/3/posts/"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 403

    @resetFixture
    Scenario: Admin can add a post to a collection
        Given that I want to make a new "Post"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {
                "id":97
            }
            """
        When I request "/collections/1/posts/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "97"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: User can view public and own private posts in a collection
        Given that I want to get all "Posts"
        And that the oauth token is "testbasicuser"
        And that the request "query string" is "status=all"
        When I request "/collections/1/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "4"

    @resetFixture
    Scenario: All users can view public posts in a collection
        Given that I want to get all "Posts"
        And that the oauth token is "testbasicuser2"
        And that the request "query string" is "status=all"
        When I request "/collections/1/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "3"

    @resetFixture
    Scenario: Admin user can view all posts in a collection
        Given that I want to get all "Posts"
        And that the oauth token is "testadminuser"
        And that the request "query string" is "status=all"
        When I request "/collections/1/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "6"
