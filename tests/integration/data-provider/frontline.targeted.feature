@dataprovidersEnabled @resetFixture
Feature: Testing the frontlinesms Data Provider with targeted surveys
    Scenario: Submit a message to frontlinesms controller for a targeted survey contact
        Given that I want to submit a new "Message"
        And that the post field "from" is "59891333224"
        And that the post field "message" is "Data Provider with targeted surveys"
        And that the post field "AccountSid" is "124"
        And that the post field "secret" is "abc123"
        And that the api_url is ""
        When I request "/sms/frontlinesms"
        Then the guzzle status code should be 200

    # Todo test this elsewhere because messages should always accept
    Scenario: Submit a message to frontlinesms controller for a contact without outgoing message fails
        Given that I want to submit a new "Message"
        And that the post field "from" is "99999992"
        And that the post field "message" is "Data Provider with targeted surveys"
        And that the post field "AccountSid" is "124"
        And that the post field "secret" is "abc123"
        And that the api_url is ""
        When I request "/sms/frontlinesms"
        Then the guzzle status code should be 200
