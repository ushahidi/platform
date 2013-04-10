@post @revisionFixture
Feature: Testing the Revisions API

    Scenario: Listing All Revisions
        Given that I want to get all "Revisions"
        When I request "/posts/99/revisions"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "2"
        Then the response status code should be 200

    Scenario: Finding a Revision
        Given that I want to find a "Revision"
        And that its "id" is "101"
        When I request "/posts/99/revisions"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "values.dummy_varchar" property equals "previous_string"
        Then the response status code should be 200

    Scenario: Finding a non-existent Revision
        Given that I want to find a "Revision"
        And that its "id" is "35"
        When I request "/posts/99/revisions"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 404

    Scenario: Trying to get Report as Revision
        Given that I want to find a "Revision"
        And that its "id" is "99"
        When I request "/posts/99/revisions"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 404

    Scenario: Creating a new Revision Fails
        Given that I want to make a new "Revision"
        And that the request "data" is:
            """
            {
                "form":1,
                "title":"Test post",
                "author":"robbie",
                "email":"robbie@ushahidi.com",
                "type":"report",
                "status":"draft",
                "values":
                {
                    "full_name":"David Kobia",
                    "description":"Skinny, homeless Kenyan last seen in the vicinity of the greyhound station",
                    "date_of_birth":"unknown",
                    "missing_date":"2012/09/25",
                    "last_location":"atlanta",
                    "status":"believed_missing"
                },
                "tags":["missing"]
            }
            """
        When I request "/posts/99/revisions"
        Then the response status code should be 405

    Scenario: Updating a Revision
        Given that I want to update a "Revision"
        And that the request "data" is:
            """
            {
                "form":1,
                "title":"Updated Test Post",
                "type":"report",
                "status":"published",
                "values":
                {
                    "full_name":"David Kobia",
                    "description":"Skinny, homeless Kenyan last seen in the vicinity of the greyhound station",
                    "date_of_birth":"unknown",
                    "missing_date":"2012/09/25",
                    "last_location":"atlanta",
                    "status":"believed_missing"
                },
                "tags":["missing","kenyan"]
            }
            """
        And that its "id" is "2"
        When I request "/posts/99/revisions"
        Then the response status code should be 405

    Scenario: Deleting a Revision
        Given that I want to delete a "Revision"
        And that its "id" is "2"
        When I request "/posts/99/revisions"
        Then the response status code should be 405
