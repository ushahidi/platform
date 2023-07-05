@oauth2Skip
Feature: Testing the Layers API
    Scenario: Creating a new Layer with invalid data fails
        Given that I want to make a new "Layer"
        And that the api_url is "api/v5"
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
        And that the api_url is "api/v5"
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
        And that the api_url is "api/v5"
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
        And that the api_url is "api/v5"
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

    