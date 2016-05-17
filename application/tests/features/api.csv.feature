@csv @oauth2Skip @dataImportEnabled
Feature: Testing the CSV API
    Scenario: Uploading a CSV file
        Given that I want to make a new "CSV"
        And that the post file "file" is "tests/datasets/ushahidi/sample.csv"
        When I request "/csv"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "columns" property
        And the "columns.0" property equals "title"
        Then the guzzle status code should be 200

    Scenario: Update CSV mapping
        Given that I want to update a "CSV"
        And that the request "data" is:
        """
        {
            "columns":["title", "name", "date", "location", "details", "lat", "lon", "actions"],
            "maps_to":["title", "full_name", null, "last_location", null, "last_location_point.lat", "last_location_point.lon", "possible_actions.0", "possible_actions.1"],
            "fixed":
            {
                "form":1,
                "tags":["explosion"],
                "status":"published",
                "published_to":["admin"]
            }
        }
        """
        And that its "id" is "1"
        When I request "/csv"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "maps_to" property
        And the "maps_to.0" property equals "title"
        Then the guzzle status code should be 200
        
    Scenario: Importing CSV
        Given that I want to import a "CSV"
        And that the request "data" is:
        """
        {}
        """
        When I request "/csv/1/import"
        Then the response is JSON
        And the response has a "processed" property
        And the type of the "processed" property is "numeric"
        And the response has a "errors" property
        And the type of the "errors" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Deleting a CSV entry
        Given that I want to delete a "CSV"
        And that its "id" is "1"
        When I request "/csv"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        Then the guzzle status code should be 200
