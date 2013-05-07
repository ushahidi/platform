@tagsFixture
Feature: Testing the Tags API

    Scenario: Creating a new Tag
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Boxes",
                "slug":"boxes",
                "type":"category",
                "priority":1
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the response status code should be 200

    Scenario: Creating a duplicate tag
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Duplicate",
                "slug":"duplicate",
                "type":"category",
                "priority":1
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 400

    Scenario: Check slug is generated on new tag
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "tag":"My magical tag",
                "type":"category",
                "priority":1
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "slug" property
        And the "slug" property equals "my-magical-tag"
        Then the response status code should be 200

    Scenario: Updating a Tag
        Given that I want to update a "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Updated",
                "slug":"updated",
                "type":"status",
                "priority":1
            }
            """
        And that its "id" is "1"
        When I request "/tags"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the response has a "tag" property
        And the "tag" property equals "Updated"
        Then the response status code should be 200

    Scenario: Updating a non-existent Tag
        Given that I want to update a "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Updated",
                "slug":"updated",
                "type":"varchar",
                "priority":1
            }
            """
        And that its "id" is "40"
        When I request "/tags"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 404

    Scenario: Listing All Tags
        Given that I want to get all "Tags"
        When I request "/tags"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the response status code should be 200

    Scenario: Finding a Tag
        Given that I want to find a "Tag"
        And that its "id" is "1"
        When I request "/tags"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the response status code should be 200

    Scenario: Finding a non-existent Tag
        Given that I want to find a "Tag"
        And that its "id" is "35"
        When I request "/tags"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 404

    Scenario: Deleting a Tag
        Given that I want to delete a "Tag"
        And that its "id" is "1"
        When I request "/tags"
        Then the response status code should be 200

    Scenario: Deleting a non-existent Tag
        Given that I want to delete a "Tag"
        And that its "id" is "35"
        When I request "/tags"
        And the response has a "errors" property
        Then the response status code should be 404
