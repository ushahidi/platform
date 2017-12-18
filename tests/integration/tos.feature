@resetFixture @tos
Feature: Testing the Tos API

    Scenario: Create a ToS entry
        Given that I want to make a new "tos"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that the request "data" is:
            """
            {
                "tos_version_date":"2017-07-14T19:12:20+00:00"
            }
            """
        When I request "/tos"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

@resetFixture
    Scenario: Anonymous users cannot create a TOS entry
        Given that I want to make a new "tos"
        And that the request "Authorization" header is "Bearer testanon"
        And that the request "data" is:
            """
            {
                "tos_version_date":"2017-07-14T19:12:20+00:00"
            }
            """
        When I request "/tos"
        Then the guzzle status code should be 403

    @resetFixture
    Scenario: Getting a ToS entry
        Given that I want to find a "Tos"
        And that the request "Authorization" header is "Bearer testbasicuser"
        When I request "/tos"
        Then the response is JSON
        And the response has a "results" property
        And the "results.0.tos_version_date" property equals "2017-07-14T19:12:20+00:00"
        Then the guzzle status code should be 200

@resetFixture
        Scenario: Anonymous users cannot get a ToS entry
        Given that I want to find a "Tos"
        And that the request "Authorization" header is "Bearer testanon"
        When I request "/tos"
        Then the guzzle status code should be 403
