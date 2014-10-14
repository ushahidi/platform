@oauth2
Feature: Testing OAuth2 endpoints

    Scenario: Requesting an Authorization code
        Given I am on "oauth/authorize?response_type=code&client_id=demoapp&redirect_uri=/&state=testing&scope=api" with redirection
        When I fill in "login-username" with "robbie"
        And I fill in "login-password" with "testing"
        And I press "login-submit"
        Then I press "authorizeButton" without redirection
        Then the response status code should be 302
        Then the redirect location should match "\?code=.*&state=testing"

    Scenario: Cancelled request for an Authorization code
        Given I am on "oauth/authorize?response_type=code&client_id=demoapp&redirect_uri=/&state=testing&scope=api" with redirection
        When I fill in "login-username" with "robbie"
        And I fill in "login-password" with "testing"
        And I press "login-submit"
        And I press "cancelButton" without redirection
        Then the response status code should be 302
        Then the redirect location should match "\?error=access_denied&error_message=.*&state=testing"

    Scenario: Requesting access token with authorization code
        Given I am on "oauth/authorize?response_type=code&client_id=ushahidiui&redirect_uri=/user/oauth&state=testing&scope=api" with redirection
        When I fill in "login-username" with "robbie"
        And I fill in "login-password" with "testing"
        And I press "login-submit"
        Then I press "authorizeButton"
        Then I should have cookie "authtoken"

    Scenario: Requesting access token with a complete redirect URI
        Given I am on "oauth/authorize?response_type=code&client_id=ushahidiui&redirect_uri=http://ushahidi.dev/user/oauth&state=testing&scope=api" with redirection
        When I fill in "login-username" with "robbie"
        And I fill in "login-password" with "testing"
        And I press "login-submit"
        Then I press "authorizeButton"
        Then I should have cookie "authtoken"

    Scenario: Requesting access token with password
        Given that I want to make a new "access_token"
        And that the request "Content-Type" header is "application/x-www-form-urlencoded"
        And that the request "data" is:
        """
          grant_type=password&client_id=demoapp&client_secret=demopass&username=robbie&password=testing
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
          grant_type=password&client_id=demoapp&client_secret=demopass&username=robbie&password=wrongpassword
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
        And that the request "Authorization" header is "Bearer testingtoken"
        And that the request "data" is:
        """
        {
            "form_id": 1,
            "locale":"en_us",
            "title": "Test post",
            "description": "testing post for oauth",
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
        And that the request "Authorization" header is "Bearer missingtoken"
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

