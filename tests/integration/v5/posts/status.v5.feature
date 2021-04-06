@post @rolesEnabled
Feature: Testing the Posts status API!
    @update
    Scenario: Admins can assign status to a post without sending a full post body!
        Given that I want to patch a "Post"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that its "id" is "1"
        And that the request "data" is:
        """
          {
              "status": "archived"
          }
        """
        When I request "/posts"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the response has a "result.status" property
        And the "result.status" property equals "archived"
        Then the guzzle status code should be 200
    @get
    Scenario: Checking that post got its status updated
        Given that I want to find a "Post"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the "result.status" property equals "archived"
        Then the guzzle status code should be 200
    @update
    Scenario: Admins can assign status to posts in bulk
        Given that I want to bulk operate on "Posts"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
        """
          {
            "operation": "patch",
            "items": [
                {
                      "status": "archived",
                      "id": 1
                },
                {
                      "status": "published",
                      "id": 99
                }
            ]
          }
        """
        When I request "/posts"
        Then the response is JSON
        Then the guzzle status code should be 200
    @update
    Scenario: Bulk status update fails on missing post
        Given that I want to bulk operate on "Posts"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
        """
          {
            "operation": "patch",
            "items": [
                {
                      "status": "archived",
                      "id": 1
                },
                {
                      "status": "published",
                      "id": 99999
                }
            ]
          }
        """
        When I request "/posts"
        Then the response is JSON
        Then the guzzle status code should be 422

    @update
    Scenario: Admins can bulk delete posts
        Given that I want to bulk operate on "Posts"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
        """
          {
            "operation": "delete",
            "items": [
                { "id": 1 },
                { "id": 99 }
            ]
          }
        """
        When I request "/posts"
        Then the response is JSON
        Then the guzzle status code should be 200

    @update
    Scenario: Bulk delete posts fails on missing post
        Given that I want to bulk operate on "Posts"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
        """
          {
            "operation": "delete",
            "items": [
                { "id": 1 },
                { "id": 99999 }
            ]
          }
        """
        When I request "/posts"
        Then the response is JSON
        Then the guzzle status code should be 422

    @resetFixture @update
    Scenario: Members cannot assign status to a post
        Given that I want to patch a "Post"
        And that the api_url is "api/v5"
        And that the oauth token is "testbasicuser"
        And that its "id" is "1"
        And that the request "data" is:
        """
          {
              "status": "archived"
          }
        """
        When I request "/posts/1/status"
        Then the response is JSON
        Then the guzzle status code should be 404
    @get
    Scenario: Checking that post did not get its status updated
        Given that I want to find a "Post"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the "result.status" property equals "published"
        Then the guzzle status code should be 200
    @update
    Scenario: Members cannot assign status to posts in bulk
        Given that I want to bulk operate on "Posts"
        And that the api_url is "api/v5"
        And that the oauth token is "testbasicuser"
        And that the request "data" is:
        """
          {
            "operation": "patch",
            "items": [
                {
                      "status": "draft",
                      "id": 1
                },
                {
                      "status": "draft",
                      "id": 99
                }
            ]
          }
        """
        When I request "/posts"
        Then the response is JSON
        Then the guzzle status code should be 403
    @update
    Scenario: Members cannot bulk delete posts
        Given that I want to bulk operate on "Posts"
        And that the api_url is "api/v5"
        And that the oauth token is "testbasicuser"
        And that the request "data" is:
        """
          {
            "operation": "delete",
            "items": [
                { "id": 1 },
                { "id": 99 }
            ]
          }
        """
        When I request "/posts"
        Then the response is JSON
        Then the guzzle status code should be 403
