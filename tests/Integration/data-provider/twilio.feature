@dataproviders
Feature: Testing the Twilio Data Provider

    Scenario: Submit a message to twilio controller
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            {
                "AccountSid": "1234",
                "Body": "Some testing message",
                "MessageSid": "123456",
                "To": "1234",
                "From": "1234"
            }
            """
        And that the api_url is ""
        When I request "sms/twilio"
        Then the guzzle status code should be 200

    Scenario: Submit a message to twilio controller with wrong sid
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            {
                "AccountSid": "wrong",
                "Body": "Some testing message",
                "MessageSid": "123456",
                "To": "1234",
                "From": "1234"
            }
            """
        And that the api_url is ""
        When I request "sms/twilio"
        Then the response is JSON
        Then the guzzle status code should be 403

    Scenario: Submit a message to twilio controller with no message
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            {
                "AccountSid": "1234",
                "MessageSid": "123456",
                "To": "1234",
                "From": "1234"
            }
            """
        And that the api_url is ""
        When I request "sms/twilio"
        Then the response is JSON
        And the response has an "errors" property
        Then the guzzle status code should be 422

    Scenario: Submit a message to twilio controller with no from
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            {
                "AccountSid": "1234",
                "Body": "Some testing message",
                "MessageSid": "123456",
                "To": "1234"
            }
            """
        And that the api_url is ""
        When I request "sms/twilio"
        Then the response is JSON
        And the response has an "errors" property
        Then the guzzle status code should be 422

