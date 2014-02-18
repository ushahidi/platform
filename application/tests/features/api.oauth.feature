@oauth2
Feature: Testing OAuth2 endpoints

    Scenario: Requesting an Authorization code
        Given I am on "oauth/authorize?response_type=code&client_id=demoapp&state=testing&scope=api" with redirection
        When I fill in "login-username" with "robbie"
        And I fill in "login-password" with "testing"
        And I press "login-submit"
        Then I press "authorizeButton" without redirection
        Then the response status code should be 302
        Then the redirect location should match "\?code=.*&state=testing"

    Scenario: Cancelled request for an Authorization code
        Given I am on "oauth/authorize?response_type=code&client_id=demoapp&state=testing&scope=api" with redirection
        When I fill in "login-username" with "robbie"
        And I fill in "login-password" with "testing"
        And I press "login-submit"
        And I press "cancelButton" without redirection
        Then the response status code should be 302
        Then the redirect location should match "\?error=access_denied&error_description=.*&state=testing"

    Scenario: Requesting access token with authorization code
        Given that I want to make a new "access_token"
        And that the request "data" is:
        """
          code=4d105df9a7f8645ef8306dd40c7b1952794bf368&grant_type=authorization_code&client_id=demoapp&client_secret=demopass
        """
        And that the api_url is ""
        Then I request "oauth/token"
        Then the response is JSON
        And the response has a "access_token" property
        Then the guzzle status code should be 200

    Scenario: Requesting access token with password
        Given that I want to make a new "access_token"
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
        And that the request "data" is:
        """
          grant_type=password&client_id=demoapp&client_secret=demopass&username=robbie&password=wrongpassword
        """
        And that the api_url is ""
        Then I request "oauth/token"
        Then the response is JSON
        And the "error" property equals "invalid_grant"
        Then the guzzle status code should be 400

    Scenario: Requesting access token with client credentials
        Given that I want to make a new "access_token"
        And that the request "data" is:
        """
          grant_type=client_credentials&client_id=demoapp&client_secret=demopass
        """
        And that the api_url is ""
        Then I request "oauth/token"
        Then the response is JSON
        And the response has a "access_token" property
        Then the guzzle status code should be 200

    Scenario: Requesting access token with refresh_token
        Given that I want to make a new "access_token"
        And that the request "data" is:
        """
          grant_type=refresh_token&client_id=demoapp&client_secret=demopass&refresh_token=5a846f5351a46fc9bdd5b8f55224b51671cf8b8f&scope=api
        """
        And that the api_url is ""
        Then I request "oauth/token"
        Then the response is JSON
        And the response has a "access_token" property
        Then the guzzle status code should be 200

    Scenario: Requesting an access token with implicit flow
        Given I am on "oauth/authorize?response_type=token&client_id=demoapp&state=testing&scope=api" with redirection
        When I fill in "login-username" with "robbie"
        And I fill in "login-password" with "testing"
        And I press "login-submit"
        And I press "authorizeButton" without redirection
        Then the response status code should be 302
        Then the redirect location should match "\#access_token=.*&expires_in=[0-9]*&token_type=bearer&scope=api&state=testing"

    Scenario: Authorized Posts Request
        Given that I want to update a "Post"
        And that its "id" is "95"
        And that the request "Authorization" header is "Bearer testingtoken"
        And that the request "data" is:
        """
        {
            "form_id": 1,
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
        Then the guzzle status code should be 401

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
        And the response has an "error" property
        And the "error" property equals "invalid_grant"
        Then the guzzle status code should be 401

# Tests for client with restricted grant types: authorization_code only!

    Scenario: Restricted client requesting an Authorization code
        Given I am on "oauth/authorize?response_type=code&client_id=restricted_app&state=testing&scope=api" with redirection
        When I fill in "login-username" with "robbie"
        And I fill in "login-password" with "testing"
        And I press "login-submit"
        And I press "authorizeButton" without redirection
        Then the response status code should be 302
        Then the redirect location should match "\?code=.*&state=testing"

    Scenario: Restricted client requesting access token with authorization code
        Given that I want to make a new "access_token"
        And that the request "data" is:
        """
          code=4d105df9a7f8645ef8306dd40c7b1952794bf372&grant_type=authorization_code&client_id=restricted_app&client_secret=demopass
        """
        And that the api_url is ""
        Then I request "oauth/token"
        Then the response is JSON
        And the response has a "access_token" property
        Then the guzzle status code should be 200

    Scenario: Restricted client requesting access token with password
        Given that I want to make a new "access_token"
        And that the request "data" is:
        """
          grant_type=password&client_id=restricted_app&client_secret=demopass&username=robbie&password=testing
        """
        And that the api_url is ""
        Then I request "oauth/token"
        Then the response is JSON
        And the response has an "error" property
        And the "error" property equals "unauthorized_client"
        Then the guzzle status code should be 400

    Scenario: Restricted client requesting an access token with implicit flow
        Given I am on "oauth/authorize?response_type=token&client_id=restricted_app&state=testing&scope=api" without redirection
        Then the response status code should be 302
        Then the redirect location should match "error=unauthorized_client&error_description=.*&state=testing"
