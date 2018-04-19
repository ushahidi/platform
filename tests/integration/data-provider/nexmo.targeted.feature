@dataprovidersEnabled @resetFixture
Feature: Testing the Nexmo Data Provider with targeted surveys
    Scenario: Submit a message to nexmo controller for a targeted survey contact
        Given that I want to submit a new "Message"
        And that the post field "msisdn" is "99999993"
        And that the post field "text" is "Data Provider with targeted surveys"
        And that the post field "secret" is "1234"
        And that the post field "to" is "199"
        And that the post field "messageId" is "2223"
        And that the post field "type" is "text"
        And that the api_url is ""
        When I request "/sms/nexmo/reply"
        Then the guzzle status code should be 200

    # Todo test this elsewhere because messages should always accept
    Scenario: Submit a message to nexmo controller for a contact without outgoing message fails
        Given that I want to submit a new "Message"
        And that the post field "msisdn" is "99999992"
        And that the post field "text" is "Data Provider with targeted surveys"
        And that the post field "secret" is "1234"
        And that the post field "to" is "199"
        And that the post field "messageId" is "2223"
        And that the post field "type" is "text"
        And that the api_url is ""
        When I request "/sms/nexmo/reply"
        Then the guzzle status code should be 200
