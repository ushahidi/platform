@oauth2
Feature: Testing OAuth2 endpoints

    Scenario: Requesting access token with password
        Given that I want to make a new "access_token"
        And that the request "Content-Type" header is "application/x-www-form-urlencoded"
        And that the request "data" is:
        """
          grant_type=password&client_id=demoapp&client_secret=demopass&username=robbie@ushahidi.com&password=testing
        """
        And that the api_url is ""
        Then I request "oauth/token"
        Then the response is JSON
        And the response has a "access_token" property
        Then the guzzle status code should be 200

    Scenario: Requesting access token with incorrect password fails
        Given that I want to make a new "access_token"
        And that the request "Content-Type" header is "application/x-www-form-urlencoded"
        And that the request "data" is:
        """
          grant_type=password&client_id=demoapp&client_secret=demopass&username=robbie@ushahidi.com&password=wrongpassword
        """
        And that the api_url is ""
        Then I request "oauth/token"
        Then the response is JSON
        And the "error" property equals "invalid_request"
        And the "error_description" property contains "credentials"
        Then the guzzle status code should be 400

    Scenario: Requesting access token with client credentials
        Given that I want to make a new "access_token"
        And that the request "Content-Type" header is "application/x-www-form-urlencoded"
        And that the request "data" is:
        """
          grant_type=client_credentials&client_id=demoapp&client_secret=demopass
        """
        And that the api_url is ""
        Then I request "oauth/token"
        Then the response is JSON
        And the response has a "access_token" property
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Authorized Posts Request
        Given that I want to update a "Post"
        And that its "id" is "95"
        And that the oauth token is "testingtoken"
        And that the request "data" is:
        """
        {
            "form_id": 1,
            "locale":"en_us",
            "title": "Test post",
            "content": "testing post for oauth",
            "status": "published"
        }
        """
        When I request "/posts"
        Then the response is JSON
        And the response has an "id" property
        Then the guzzle status code should be 200

    Scenario: Authorized Posts Request (access_token in query string)
        Given that I want to find a "Post"
        And that its "id" is "95"
        And that the request "query string" is:
        """
            access_token=testingtoken
        """
        When I request "/posts"
        Then the response is JSON
        And the response has an "id" property
        Then the guzzle status code should be 200

    Scenario: Unauthorized Posts Request (no token)
        Given that I want to update a "Post"
        And that its "id" is "95"
        And that the request "data" is:
        """
        {
            "title": "Test post",
            "description": "testing post for oauth",
            "status": "published"
        }
        """
        When I request "/posts"
        Then the response is JSON
        And the response has an "errors" property
        Then the guzzle status code should be 400

    Scenario: Unauthorized Posts Request (invalid token)
        Given that I want to update a "Post"
        And that its "id" is "95"
        And that the oauth token is "missingtoken"
        And that the request "data" is:
        """
        {
            "title": "Test post",
            "description": "testing post for oauth",
            "status": "published"
        }
        """
        When I request "/posts"
        Then the response is JSON
        And the response has an "errors" property
        And the "WWW-Authenticate" header should exist
        Then the guzzle status code should be 401

