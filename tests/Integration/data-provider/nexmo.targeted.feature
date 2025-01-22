@dataprovidersEnabled @resetFixture
Feature: Testing the Nexmo Data Provider with targeted surveys
    Scenario: Submit a message to nexmo controller for a targeted survey contact
        Given that I want to get all "Message"
        And that the request "query string" is:
            """
            text=survey%20reply&msisdn=99999993&to=199&&messageId=2223
            """
        And that the api_url is ""
        When I request "/sms/nexmo/reply"
        Then the guzzle status code should be 200

    # Todo test this elsewhere because messages should always accept
    Scenario: Submit a message to nexmo controller for a contact without outgoing message fails
        Given that I want to get all "Message"
        And that the request "query string" is:
            """
            text=survey%20not%20reply&msisdn=99999992&to=199&messageId=2223
            """
        And that the api_url is ""
        When I request "/sms/nexmo/reply"
        Then the guzzle status code should be 200
