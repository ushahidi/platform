@dataproviders
Feature: Testing the FrontlineSms Data Provider

    Scenario: Submit a message to nexmo controller
        Given that I want to find a "Message"
        And that the request "query string" is:
            """
            secret=1234&text=Some+testing+message&msisdn=123456789&to=123&messageId=1&type=text
            """
        And that the api_url is ""
        When I request "sms/nexmo"
        Then the response is JSON
        And the "success" property is true
        Then the guzzle status code should be 200

    Scenario: Submit a message to nexmo controller with no message
        Given that I want to find a "Message"
        And that the request "query string" is:
            """
            secret=1234&msisdn=123456789&to=123&messageId=1&type=text
            """
        And that the api_url is ""
        When I request "sms/nexmo"
        Then the response is JSON
        And the response has an "errors" property
        And the "errors.0.message" property equals "Invalid message"
        And the "errors.0.status" property equals "400"
        Then the guzzle status code should be 400

    Scenario: Submit a message to nexmo controller with no from
        Given that I want to find a "Message"
        And that the request "query string" is:
            """
            secret=1234&text=Some+testing+message&to=123&messageId=1&type=text
            """
        And that the api_url is ""
        When I request "sms/nexmo"
        Then the response is JSON
        And the response has an "errors" property
        And the "errors.0.message" property equals "Invalid message"
        And the "errors.0.status" property equals "400"
        Then the guzzle status code should be 400

