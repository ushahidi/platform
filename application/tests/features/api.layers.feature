@oauth2Skip
Feature: Testing the Layers API

    Scenario: Listing All Layers
        Given that I want to get all "Layers"
        When I request "/layers"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "3"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Layers by type
        Given that I want to get all "Layers"
        And that the request "query string" is:
            """
            type=geojson
            """
        When I request "/layers"
        Then the response is JSON
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Layers by active
        Given that I want to get all "Layers"
        And that the request "query string" is:
            """
            active=0
            """
        When I request "/layers"
        Then the response is JSON
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    Scenario: Finding a Layer
        Given that I want to find a "Layer"
        And that its "id" is "1"
        When I request "/layers"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Layer
        Given that I want to find a "Layer"
        And that its "id" is "35"
        When I request "/layers"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404