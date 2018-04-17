@tagsFixture @oauth2Skip
Feature: Testing the Tags API

    Scenario: Creating a new Tag
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "parent_id":1,
                "tag":"Boxes",
                "slug":"boxes",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role": ["admin", "user"]
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "tag" property equals "Boxes"
        And the "slug" property equals "boxes"
        And the "description" property equals "Is this a box? Awesome"
        And the "color" property equals "#00ff00"
        And the "priority" property equals "1"
        And the "type" property equals "category"
        And the response has a "role" property
        And the "parent.id" property equals "1"
        Then the guzzle status code should be 200

    Scenario: Creating a duplicate tag
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Duplicate",
                "type":"category",
                "priority":1
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    Scenario: Creating a tag with a duplicate slug
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Something",
                "slug":"duplicate",
                "type":"category",
                "priority":1
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    Scenario: Creating a tag with a long name fails
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Really really really really really long, Really really really really really long, Really really really really really long, Really really really really really long, Really really really really really long, Really really really really really long, Really really really really really long, Really really really really really long, Really really really really really long, Really really really really really long, Really really really really really long, Really really really really really long",
                "type":"category"
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

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
        Then the guzzle status code should be 200

    Scenario: Check hash on color input has no effect when creating tag
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Another tag",
                "type":"category",
                "priority":1,
                "color":"#00ff00"
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "color" property equals "#00ff00"
        Then the guzzle status code should be 200

    Scenario: Creating a tag with non-existent parent fails
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Superduper tag",
                "type":"category",
                "priority":1,
                "parent_id":10001
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

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
        Then the guzzle status code should be 200

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
        Then the guzzle status code should be 404

    @resetFixture
    Scenario: Updating Tag Role Restrictions
        Given that I want to update a "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Change Role",
                "slug":"change-role",
                "type":"status",
                "role":["user"]
            }
            """
        And that its "id" is "1"
        When I request "/tags"
        Then the response is JSON
        And the response has a "id" property
        And the "id" property equals "1"
        And the response has a "role" property
        And the "role.0" property equals "user"
        Then the guzzle status code should be 200

    Scenario: Removing Tag Role Restrictions
        Given that I want to update a "Tag"
        And that the request "data" is:
            """
            {
                "tag":"Change Role",
                "slug":"change-role",
                "type":"status",
                "role":[]
            }
            """
        And that its "id" is "1"
        When I request "/tags"
        Then the response is JSON
        And the response has a "id" property
        And the "id" property equals "1"
        And the response has a "role" property
        And the "role" property is empty
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing All Tags
        Given that I want to get all "Tags"
        When I request "/tags"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "11"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Tags
        Given that I want to get all "Tags"
        And that the request "query string" is:
            """
            q=Explo
            """
        When I request "/tags"
        Then the response is JSON
        And the "count" property equals "1"
        And the "results.0.tag" property equals "Explosion"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Tags by type
        Given that I want to get all "Tags"
        And that the request "query string" is:
            """
            type=category
            """
        When I request "/tags"
        Then the response is JSON
        And the "count" property equals "9"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Tags by parent
        Given that I want to get all "Tags"
        And that the request "query string" is:
            """
            parent_id=3
            """
        When I request "/tags"
        Then the response is JSON
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    Scenario: Finding a Tag
        Given that I want to find a "Tag"
        And that its "id" is "1"
        When I request "/tags"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Tag
        Given that I want to find a "Tag"
        And that its "id" is "35"
        When I request "/tags"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Tag
        Given that I want to delete a "Tag"
        And that its "id" is "1"
        When I request "/tags"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Deleting a tag removes it from attribute options
        Given that I want to delete a "Tag"
        And that its "id" is "1"
        When I request "/tags"
        Then the guzzle status code should be 200
        Given that I want to find a "Attribute"
        And that its "id" is "26"
        When I request "/forms/1/attributes"
        Then the response is JSON
        And the response has an "options" property
        And the "options" property does not contain "1"
        Then the guzzle status code should be 200

    Scenario: Deleting a non-existent Tag
        Given that I want to delete a "Tag"
        And that its "id" is "35"
        When I request "/tags"
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Creating a new child for a tag with role=admin
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "parent_id":9,
                "tag":"Valid child",
                "slug":"valid-child",
                "description":"I am a valid tag",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role": "admin"
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "tag" property equals "Valid child"
        And the "slug" property equals "valid-child"
        And the "description" property equals "I am a valid tag"
        And the "color" property equals "#00ff00"
        And the "priority" property equals "1"
        And the "type" property equals "category"
        And the response has a "role" property
        And the type of the "role" property is "array"
        And the "parent.id" property equals "9"
        Then the guzzle status code should be 200

    Scenario: Creating a new invalid child for a tag with role=admin
        Given that I want to make a new "Tag"
        And that the request "data" is:
            """
            {
                "parent_id":9,
                "tag":"Not a valid tag role",
                "slug":"not-valid-tag-role",
                "description":"My role is invalid",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role":"nope"
            }
            """
        When I request "/tags"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422
