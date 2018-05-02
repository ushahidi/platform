@apikeys
Feature: Testing the ApiKey API

    Scenario: Create an apikey
        Given that I want to make a new "ApiKey"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
          """
          {
          }
          """
        When I request "/apikeys"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: An anonymous user cannot create to a apikey
        Given that I want to make a new "Apikey"
        And that the oauth token is "testanon"
        And that the request "data" is:
            """
            {
                "api_key": "myshadykey"
            }
            """
        When I request "/apikeys"
        Then the guzzle status code should be 403

    Scenario: Updating an apikey
        Given that I want to update an "ApiKey"
        And that its "id" is "2"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
        """
        {
            "api_key": "mytestkeyisthislong"
        }
        """
        When I request "/apikeys"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "2"
        And the "api_key" property does not contain "mytestkeyisthislong"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing Apikeys for a user
        Given that I want to get all "Apikeys"
        And that the oauth token is "testadminuser"
        When I request "/apikeys"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the guzzle status code should be 200
