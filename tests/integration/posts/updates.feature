@post @oauth2Skip
Feature: Testing the Updates API

    @resetFixture
    Scenario: Listing All Updates
        Given that I want to get all "Updates"
        When I request "/posts/99/updates"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    Scenario: Listing All Updates on a non-existent Post
        Given that I want to get all "Updates"
        When I request "/posts/999/updates"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    @resetFixture
    Scenario: Search All Posts by form id
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            form=2
            """
        When I request "/posts/99/updates"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    Scenario: Finding a Update
        Given that I want to find a "Update"
        And that its "id" is "101"
        When I request "/posts/99/updates"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Update
        Given that I want to find a "Update"
        And that its "id" is "35"
        When I request "/posts/99/updates"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Fail to find a Report as Update
        Given that I want to find a "Update"
        And that its "id" is "99"
        When I request "/posts/99/updates"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Creating a new Update
        Given that I want to make a new "Update"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test update",
                "content": "Some description",
                "status": "published",
                "type": "report",
                "locale":"en_US",
                "values": {
                    "test_varchar": ["testing"],
                    "last_location": ["blah"]
                },
                "tags": ["disaster"]
            }
            """
        When I request "/posts/99/updates"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "title" property
        And the "title" property equals "Test update"
        Then the guzzle status code should be 200

    Scenario: Updating a Update
        Given that I want to update a "Update"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test update updated",
                "content": "Some description",
                "status": "published",
                "type": "report",
                "locale":"en_US",
                "values": {
                    "test_varchar": ["testing"],
                    "last_location": ["blah"]
                },
                "tags": ["disaster"]
            }
            """
        And that its "id" is "101"
        When I request "/posts/99/updates"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "title" property
        And the "title" property equals "Test update updated"
        Then the guzzle status code should be 200

    Scenario: Updating a non-existent Update
        Given that I want to update a "Update"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test update updated",
                "content": "Some description",
                "status": "published",
                "type": "revision",
                "locale":"de_DE",
                "values": {
                    "test_varchar": "testing",
                    "last_location": "blah"
                },
                "tags": ["update-test"]
            }
            """
        And that its "id" is "40"
        When I request "/posts/99/updates"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Updating a Update with non-existent Post
        Given that I want to update a "Update"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test update updated",
                "content": "Some description",
                "status": "published",
                "type": "revision",
                "locale":"de_DE",
                "values": {
                    "test_varchar": "testing",
                    "last_location": "blah"
                },
                "tags": ["update-test"]
            }
            """
        And that its "id" is "101"
        When I request "/posts/35/updates"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Update
        Given that I want to delete a "Update"
        And that its "id" is "101"
        When I request "/posts/99/updates"
        Then the response is JSON
        And the response has a "id" property
        Then the guzzle status code should be 200

    Scenario: Fail to delete a non existent Update
        Given that I want to delete a "Update"
        And that its "id" is "200"
        When I request "/posts/99/updates"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404
