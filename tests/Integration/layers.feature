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

    Scenario: Creating a new Layer
        Given that I want to make a new "Layer"
        And that the request "data" is:
            """
            {
                "name":"test",
                "data_url":"http://ushahidi-platform.dev/media/test.geojson",
                "type":"geojson",
                "active":true,
                "visible_by_default":true,
                "options":{
                    "noop": true
                }
            }
            """
        When I request "/layers"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "name" property equals "test"
        And the "data_url" property equals "http://ushahidi-platform.dev/media/test.geojson"
        And the "type" property equals "geojson"
        And the "active" property equals "1"
        And the "visible_by_default" property equals "1"
        And the type of the "options" property is "array"
        Then the guzzle status code should be 200

    Scenario: Creating a new Layer with invalid data fails
        Given that I want to make a new "Layer"
        And that the request "data" is:
            """
            {
                "name":"test",
                "data_url":"http://ushahidi-platform.dev/media/test.geojson",
                "type":"shape",
                "active":true,
                "visible_by_default":true
            }
            """
        When I request "/layers"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    Scenario: Creating a new Layer with missing data fails
        Given that I want to make a new "Layer"
        And that the request "data" is:
            """
            {
                "name":"test",
                "data_url":"http://ushahidi-platform.dev/media/test.geojson",
                "type":"geojson"
            }
            """
        When I request "/layers"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    Scenario: Creating a new Layer with no url or media fails
        Given that I want to make a new "Layer"
        And that the request "data" is:
            """
            {
                "name":"test",
                "type":"geojson",
                "active":true,
                "visible_by_default":true
            }
            """
        When I request "/layers"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    Scenario: Creating a new Layer invalid media fails
        Given that I want to make a new "Layer"
        And that the request "data" is:
            """
            {
                "name":"test",
                "type":"geojson",
                "active":true,
                "visible_by_default":true,
                "media_id":9999
            }
            """
        When I request "/layers"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    Scenario: Updating a Layer
        Given that I want to update a "Layer"
        And that the request "data" is:
            """
            {
                "name":"test updated",
                "data_url":"http://ushahidi-platform.dev/media/updated.geojson",
                "type":"geojson",
                "active":true,
                "visible_by_default":true
            }
            """
        And that its "id" is "1"
        When I request "/layers"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the response has a "name" property
        And the "name" property equals "test updated"
        Then the guzzle status code should be 200

    Scenario: Deleting a Layer
        Given that I want to delete a "Layer"
        And that its "id" is "1"
        When I request "/layers"
        Then the guzzle status code should be 200

    Scenario: Deleting a non-existent Layer
        Given that I want to delete a "Layer"
        And that its "id" is "35"
        When I request "/layers"
        And the response has a "errors" property
        Then the guzzle status code should be 404
