Feature: Testing the FrontlineSms Data Provider

    Scenario: Submit a message to frontlinesms controller
        Given that I want to submit a new "Message"
        And that the post field "from" is "12345678"
        And that the post field "message" is "test"
        And that the post field "secret" is "abc123"
        And that the api_url is ""
        When I request "frontlinesms"
        Then the response is JSON
        And the response has a "payload" property
        And the response has a "payload.success" property
        And the "payload.success" property is true
        Then the guzzle status code should be 200

    Scenario: Submit a message to frontlinesms controller with no message
        Given that I want to submit a new "Message"
        And that the post field "from" is "12345678"
        And that the post field "secret" is "abc123"
        And that the api_url is ""
        When I request "frontlinesms"
        Then the response is JSON
        And the response has an "errors" property
        And the "errors.0.message" property equals "Missing message"
        And the "errors.0.status" property equals "400"
        Then the guzzle status code should be 400

    Scenario: Submit a message to frontlinesms controller with no from
        Given that I want to submit a new "Message"
        And that the post field "message" is "A message"
        And that the post field "secret" is "abc123"
        And that the api_url is ""
        When I request "frontlinesms"
        Then the response is JSON
        And the response has an "errors" property
        And the "errors.0.message" property equals "Missing from"
        And the "errors.0.status" property equals "400"
        Then the guzzle status code should be 400

    Scenario: Submit a message to frontlinesms controller with incorrect secret
        Given that I want to submit a new "Message"
        And that the post field "from" is "12345678"
        And that the post field "message" is "test"
        And that the post field "secret" is "wrong"
        And that the api_url is ""
        When I request "frontlinesms"
        Then the response is JSON
        Then the guzzle status code should be 403
