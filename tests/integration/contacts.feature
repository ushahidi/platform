@contacts
Feature: Testing the Contacts API
    Scenario: Add a contact
        Given that I want to make a new "Contact"
        And that the oauth token is "testbasicuser"
        And that the request "data" is:
            """
            {
                "contact":"234567890",
                "type":"phone"
            }
            """
        When I request "/contacts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "contact" property equals "234567890"
        Then the guzzle status code should be 200

    Scenario: Update a contact
        Given that I want to update a "Contact"
        And that the oauth token is "testbasicuser"
        And that the request "data" is:
            """
            {
                "contact":"987654321"
            }
            """
        And that its "id" is "1"
        When I request "/contacts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the "contact" property equals "987654321"
        Then the guzzle status code should be 200

    Scenario: Update a contact to receive notifications
        Given that I want to update a "Contact"
        And that the oauth token is "testbasicuser"
        And that the request "data" is:
            """
            {
                "can_notify":"1"
            }
            """
        And that its "id" is "1"
        When I request "/contacts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the "can_notify" property equals "1"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing Contacts for a user
        Given that I want to get all "Contacts"
        And that the oauth token is "testbasicuser"
        And that the request "query string" is:
            """
                user=me
            """
        When I request "/contacts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "2"
        Then the guzzle status code should be 200
