@2fa @oauth2Skip @resetFixture
Feature: Testing 2fa Enable
    Scenario: Enable 2fa for user
        Given that I want to enable a "user"
        And that the request "data" is:
            """
            {
            }
            """
        When I request "/users/me/2fa"
        Then the response is JSON
        And the response has a "google2fa_url" property
        Then the guzzle status code should be 200
