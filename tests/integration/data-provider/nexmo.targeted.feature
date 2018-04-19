@dataprovidersEnabled @resetFixture
Feature: Testing the Nexmo Data Provider with targeted surveys
    Scenario: Submit a message to nexmo controller for a targeted survey contact
        Given that I want to submit a new "Message"
        And that the request "data" is:
            """
            /sms/nexmo/reply?text=Data%20Provider%20with%20targeted%20surveys&msisdn=99999993&from=99999993&to=199&&messageId=2223
            """
        And that the api_url is ""
        When I request "/sms/nexmo/reply?text=Data%20Provider%20with%20targeted%20surveys&msisdn=99999993&to=199&&messageId=2223"
        Then the guzzle status code should be 200

    # Todo test this elsewhere because messages should always accept
    Scenario: Submit a message to nexmo controller for a contact without outgoing message fails
        Given that I want to get all "Message"
        And that the request "data" is:
            """
            /sms/nexmo/reply?text=Data Provider with targeted surveys&msisdn=99999992&to=199&messageId=2223
            """
        And that the api_url is ""
        When I request "/sms/nexmo/reply"
        Then the guzzle status code should be 200
