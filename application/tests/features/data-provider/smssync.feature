@dataproviders
Feature: Testing the SMSSync Data Provider

    Scenario: Submit a message to smssync controller
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            secret=1234&message=Some+testing+message&from=123456789&sent_to=123
            """
        And that the request "Content-Type" header is "application/x-www-form-urlencoded"
        And that the api_url is ""
        Then I request "smssync"
        Then the response is JSON
        And the response has a "payload" property
        And the response has a "payload.success" property
        And the "payload.success" property is true
        Then the guzzle status code should be 200

    Scenario: Submit a message to smssync controller with wrong key
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            secret=wrong&message=Some+testing+message&from=123456789&sent_to=123
            """
        And that the request "Content-Type" header is "application/x-www-form-urlencoded"
        And that the api_url is ""
        Then I request "smssync"
        Then the response is JSON
        And the response has a "payload" property
        And the response has a "payload.success" property
        And the response has a "payload.error" property
        And the "payload.success" property is false
        And the "payload.error" property equals "Incorrect or missing secret key"
        Then the guzzle status code should be 403

    Scenario: Submit a message to smssync controller with no message
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            secret=1234&from=123456789&sent_to=123
            """
        And that the request "Content-Type" header is "application/x-www-form-urlencoded"
        And that the api_url is ""
        Then I request "smssync"
        Then the response is JSON
        And the response has a "payload" property
        And the response has a "payload.success" property
        And the response has a "payload.error" property
        And the "payload.success" property is false
        And the "payload.error" property equals "Missing message"
        Then the guzzle status code should be 400
    Scenario: Submit a message to smssync controller with no from value
        Given that I want to make a new "Message"
        And that the request "data" is:
            """
            secret=1234&sent_to=123
            """
        And that the request "Content-Type" header is "application/x-www-form-urlencoded"
        And that the api_url is ""
        Then I request "smssync"
        Then the response is JSON
        And the response has a "payload" property
        And the response has a "payload.success" property
        And the response has a "payload.error" property
        And the "payload.success" property is false
        And the "payload.error" property equals "Missing from value"
        Then the guzzle status code should be 400
