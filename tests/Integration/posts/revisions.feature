@post @oauth2Skip
Feature: Testing the Revisions API

    @resetFixture
    Scenario: Listing All Revisions
        Given that I want to get all "Revisions"
        When I request "/posts/99/revisions"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    Scenario: Listing All Revisions on a non-existent Post
        Given that I want to get all "Revisions"
        When I request "/posts/999/revisions"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Finding a Revision
        Given that I want to find a "Revision"
        And that its "id" is "103"
        When I request "/posts/99/revisions"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "values.test_varchar" property contains "previous_string"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Revision
        Given that I want to find a "Revision"
        And that its "id" is "35"
        When I request "/posts/99/revisions"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Fail to find Revision through Posts api
        Given that I want to find a "Revision"
        And that its "id" is "103"
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Trying to get Report as Revision
        Given that I want to find a "Revision"
        And that its "id" is "99"
        When I request "/posts/99/revisions"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

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
                "locale":"en_US",
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
        Then the guzzle status code should be 405

    Scenario: Updating a Revision
        Given that I want to update a "Revision"
        And that the request "data" is:
            """
            {
                "form":1,
                "title":"Updated Test Post",
                "type":"report",
                "status":"published",
                "locale":"en_US",
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
        And that its "id" is "103"
        When I request "/posts/99/revisions"
        Then the guzzle status code should be 405

    Scenario: Deleting a Revision
        Given that I want to delete a "Revision"
        And that its "id" is "103"
        When I request "/posts/99/revisions"
        Then the guzzle status code should be 405

    # @resetFixture
    # Scenario: Updating a Post creates a new revision
    #     Given that I want to update a "Post"
    #     And that the request "data" is:
    #         """
    #         {
    #             "form": 1,
    #             "title": "Should be returned when Searching",
    #             "content": "Some description",
    #             "status": "published",
    #             "type": "revision",
    #             "email": null,
    #             "author": null,
    #             "slug": "should-be-returned-when-searching",
    #             "locale":"en_US",
    #             "values": {
    #                 "test_varchar": "updated",
    #                 "last_location": "blah"
    #             },
    #             "tags": []
    #         }
    #         """
    #     And that its "id" is "99"
    #     Then I request "/posts"
    #     Then the response is JSON
    #     Then the guzzle status code should be 200
    #     # Start 2nd request
    #     Given that I want to get all "Revisions"
    #     When I request "/posts/99/revisions"
    #     Then the response is JSON
    #     And the response has a "count" property
    #     And the type of the "count" property is "numeric"
    #     And the "count" property equals "3"
    #     Then the guzzle status code should be 200
