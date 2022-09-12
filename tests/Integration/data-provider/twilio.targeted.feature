@dataprovidersEnabled @resetFixture
Feature: Testing the Twilio Data Provider with targeted surveys
    Scenario: Submit a message to twilio controller for a targeted survey contact
        Given that I want to submit a new "Message"
        And that the post field "From" is "999999933"
        And that the post field "Body" is "Data Provider with targeted surveys"
        And that the post field "AccountSid" is "1234"
        And that the api_url is ""
        When I request "/sms/twilio/reply"
        Then the guzzle status code should be 200

    # Todo test this elsewhere because messages should always accept
    Scenario: Submit a message to twilio controller for a contact without outgoing message fails
        Given that I want to submit a new "Message"
        And that the post field "From" is "99999992"
        And that the post field "Body" is "Data Provider with targeted surveys"
        And that the post field "AccountSid" is "1234"
        And that the api_url is ""
        When I request "/sms/twilio/reply"
        Then the guzzle status code should be 200
