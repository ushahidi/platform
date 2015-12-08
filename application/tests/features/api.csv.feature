@csv @oauth2Skip
Feature: Testing the CSV API
    Scenario: Uploading a CSV file
        Given that I want to make a new "CSV"
        And that the post field "form_id" is "1"
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
            "columns":["title", "name", "date", "location", "details"],
            "maps_to":["title", "full_name", null, "last_location", null],
            "unmapped":["date_of_birth", "description"]
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

    Scenario: Finish CSV import
        Given that I want to update a "CSV"
        And that the request "data" is:
        """
        {
            "columns":["title", "name", "date", "location", "details"],
            "maps_to":["title", "full_name", null, "last_location", null],
            "unmapped":
            {
                "missing_status": ["believed_missing"]
            },
            "tags":["explosion"],
            "status":"published",
            "published_to":["admin"],
            "completed":true
        }
        """
        And that its "id" is "1"
        When I request "/csv"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "tags" property
        And the "tags" property contains "explosion"
        And the response has a "status" property
        And the "status" property equals "published"
        And the response has a "published_to" property
        And the "published_to" property contains "admin"
        And the response has a "completed" property
        And the "completed" property is true
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


