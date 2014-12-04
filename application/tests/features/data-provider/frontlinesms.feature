@dataproviders
Feature: Testing the FrontlineSms Data Provider

    Scenario: Submit a message to frontlinesms controller
        Given that I want to submit a new "Message"
        And that the request "query string" is:
            """
            key=1234&m=Some+testing+message&f=123456789
            """
        And that the api_url is ""
        When I request "frontlinesms"
        Then the response is JSON
        And the response has a "payload" property
        And the response has a "payload.success" property
        And the "payload.success" property is true
        Then the guzzle status code should be 200

    Scenario: Submit a message to frontlinesms controller with wrong key
        Given that I want to submit a new "Message"
        And that the request "query string" is:
            """
            key=wrong&m=Some+testing+message&f=123456789
            """
        And that the api_url is ""
        When I request "frontlinesms"
        Then the response is JSON
        And the response has a "payload" property
        And the response has a "payload.success" property
        And the response has a "payload.error" property
        And the "payload.success" property is false
        And the "payload.error" property equals "Incorrect or missing key"
        Then the guzzle status code should be 403

    Scenario: Submit a message to frontlinesms controller with no message
        Given that I want to submit a new "Message"
        And that the request "query string" is:
            """
            key=1234&f=123456789
            """
        And that the api_url is ""
        When I request "frontlinesms"
        Then the response is JSON
        And the response has a "payload" property
        And the response has a "payload.success" property
        And the response has a "payload.error" property
        And the "payload.success" property is false
        And the "payload.error" property equals "Missing message"
        Then the guzzle status code should be 403

    Scenario: Submit a message to frontlinesms controller with no message
        Given that I want to submit a new "Message"
        And that the request "query string" is:
            """
            key=1234&f=123456789
            """
        And that the api_url is ""
        When I request "frontlinesms"
        Then the response is JSON
        And the response has a "payload" property
        And the response has a "payload.success" property
        And the response has a "payload.error" property
        And the "payload.success" property is false
        And the "payload.error" property equals "Missing message"
        Then the guzzle status code should be 403
