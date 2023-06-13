@oauth2Skip
Feature: Testing the Messages API


    Scenario: Get all messages regardless of status
        Given that I want to get all "Messages"
        And that the api_url is "api/v5"
        And that the request "query string" is:
            """
            status=all
            """
        When I request "/messages"
        Then the response is JSON
        And the "count" property equals "19"
        Then the guzzle status code should be 200

    Scenario: Finding a Message
        Given that I want to find a "Message"
        And that the api_url is "api/v5"
        And that its "id" is "1"
        When I request "/messages"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Message
        Given that I want to find a "Message"
        And that the api_url is "api/v5"
        And that its "id" is "35"
        When I request "/messages"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    
   
    Scenario: Admin cant set user id to something invalid
        Given that I want to make a new "Message"
        And that the api_url is "api/v5"
        And that the request "data" is:
            """
            {
                "message":"Test creating outgoing",
                "type":"sms",
                "direction":"outgoing",
                "contact_id":"1",
                "user_id":57
            }
            """
        When I request "/messages"
        Then the response is JSON
        Then the guzzle status code should be 422
