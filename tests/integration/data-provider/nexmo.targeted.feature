@dataprovidersEnabled @resetFixture
Feature: Testing the Nexmo Data Provider with targeted surveys
    Scenario: Submit a message to nexmo controller for a targeted survey contact
        Given that I want to submit a new "Message"
        And that the post field "from" is "999999933"
        And that the post field "text" is "Data Provider with targeted surveys"
        And that the api_url is ""
        When I request "/sms/nexmo/reply?text=Data%20Provider%20with%20targeted%20surveys&msisdn=123&to=222&messageId=2223"
        Then the guzzle status code should be 200
    Scenario: Submit a message to nexmo controller for a contact without outgoing message fails
        Given that I want to submit a new "Message"
        And that the post field "from" is "99999992"
        And that the post field "text" is "Data Provider with targeted surveys"
        And that the api_url is ""
        When I request "/sms/nexmo/reply?text=Data Provider with targeted surveys&msisdn=123&to=222&messageId=2223"
        And the response should contain "Outgoing question not found for contact 7 and form 7"
        Then the guzzle status code should be 400