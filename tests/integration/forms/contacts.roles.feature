Feature: Testing the Form Contacts API
    @resetFixture
    Scenario: Creating a valid set of Contacts for a targeted survey
        Given that I want to make a new "Contact"
        And that the request "data" is:
            """
            {
                "contacts":"99333222, 91333222",
                "country_code": "UY"
            }
            """
        And that the request "Authorization" header is "Bearer testadminuser"
        When I request "/forms/5/contacts"
        Then the response is JSON
        And the response has a "form_id" property
        And the type of the "form_id" property is "numeric"
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the guzzle status code should be 200
    Scenario: Getting a valid set of Contacts for a targeted survey
        Given that I want to get all "Contacts"
        And that the request "Authorization" header is "Bearer testbasicuser"
        When I request "/forms/5/contacts"
        Then the response is JSON
        Then the guzzle status code should be 403

    Scenario: Getting a valid set of Contacts for a targeted survey (admin)
        Given that I want to get all "Contacts"
        And that the request "Authorization" header is "Bearer testadminuser"
        When I request "/forms/5/contacts"
        Then the response is JSON
        Then the guzzle status code should be 200
    @resetFixture
    Scenario: Creating a valid set of Contacts for a targeted survey
        Given that I want to make a new "Contact"
        And that the request "data" is:
            """
            {
                "contacts":"99333222, 91333222",
                "country_code": "UY"
            }
            """
        And that the request "Authorization" header is "Bearer testbasicuser"
        When I request "/forms/5/contacts"
        Then the response is JSON
        Then the guzzle status code should be 403
