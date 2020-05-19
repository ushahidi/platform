@tagsFixture @rolesEnabled
Feature: Testing the Categories API
    Scenario: Creating a new Tag
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
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
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the "result.tag" property equals "Boxes"
        And the "result.slug" property equals "boxes"
        And the "result.description" property equals "Is this a box? Awesome"
        And the "result.color" property equals "#00ff00"
        And the "result.priority" property equals "1"
        And the "result.type" property equals "category"
        And the response has a "result.role" property
        And the "result.parent.id" property equals "1"
        Then the guzzle status code should be 201

    Scenario: Creating a duplicate tag
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        And that the request "data" is:
            """
            {
                "parent_id":1,
                "tag":"Boxes",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role": ["admin", "user"]
            }
            """
        When I request "/categories"
        Then the response is JSON
        And the response has a "slug" property
        And the "slug.0" property equals "Slug must be unique"
        And the response has a "tag" property
        And the "tag.0" property equals "Tag must be unique"
        Then the guzzle status code should be 422

    Scenario: Creating a tag with a duplicate slug
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        And that the request "data" is:
            """
            {
                "parent_id":1,
                "tag":"Boxes are fun",
                "slug": "boxes",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role": ["admin", "user"]
            }
            """
        When I request "/categories"
        Then the response is JSON
        And the response has a "slug" property
        And the "slug.0" property equals "Slug must be unique"
        Then the guzzle status code should be 422
    Scenario: Creating a tag with a long name fails
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        And that the request "data" is:
            """
            {
                "parent_id":1,
                "tag":"Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome Is this a box? Awesome",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role": ["admin", "user"]
            }
            """
        When I request "/categories"
        Then the response is JSON
        And the response has a "tag" property
        And the "tag.0" property equals "Tag must not exceed 255 characters long"
        Then the guzzle status code should be 422

    Scenario: Check slug is generated on new tag
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        And that the request "data" is:
            """
            {
                "parent_id":1,
                "tag":"I expect tags",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role": ["admin", "user"]
            }
            """
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the response has a "result.slug" property
        And the "result.slug" property equals "i-expect-tags"
        Then the guzzle status code should be 201

    Scenario: Check hash on color input has no effect when creating tag
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        And that the request "data" is:
            """
            {
                "parent_id":1,
                "tag":"I expect tags oo",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"#00ff00",
                "role": ["admin", "user"]
            }
            """
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the "result.color" property equals "#00ff00"
        Then the guzzle status code should be 201

    Scenario: Creating a tag with non-existent parent fails
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        And that the request "data" is:
            """
            {
                "parent_id":123456,
                "tag":"I expect tags",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"#00ff00",
                "role": ["admin", "user"]
            }
            """
        When I request "/categories"
        Then the response is JSON
        And the response has a "parent_id" property
        And the "parent_id.0" property equals "Parent category must exist"
        Then the guzzle status code should be 422

    Scenario: Updating a Tag
        Given that I want to update a "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
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
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the "result.id" property equals "1"
        And the response has a "result.tag" property
        And the "result.tag" property equals "Updated"
        Then the guzzle status code should be 200

    Scenario: Updating a non-existent Tag
        Given that I want to update a "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
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
        When I request "/categories"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    @resetFixture
    Scenario: Updating Tag Role Restrictions
        Given that I want to update a "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
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
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the "result.id" property equals "1"
        And the response has a "result.role" property
        And the "result.role" property count is "1"
        And the "result.role.0" property equals "user"
        Then the guzzle status code should be 200

    Scenario: Removing Tag Role Restrictions
        Given that I want to update a "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
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
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the "result.id" property equals "1"
        And the response has a "result.role" property
        And the "result.role" property is empty
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing All Tags available to admins
        Given that I want to get all "Categories"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        When I request "/categories"
        Then the response is JSON
        And the "results" property count is "7"
        Then the guzzle status code should be 200
    Scenario: Listing All Tags available to regular users
        Given that I want to get all "Categories"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        When I request "/categories"
        Then the response is JSON
        And the "results" property count is "6"
        Then the guzzle status code should be 200
    Scenario: Listing All Tags available to non-users
        Given that I want to get all "Categories"
        And that the api_url is "api/v4"
        When I request "/categories"
        Then the response is JSON
        And the "results" property count is "5"
        Then the guzzle status code should be 200

#    @resetFixture
#    Scenario: Search All Tags
#        Given that I want to get all "Tags"
#        And that the request "query string" is:
#            """
#            q=Explo
#            """
#        When I request "/tags"
#        Then the response is JSON
#        And the "count" property equals "1"
#        And the "results.0.tag" property equals "Explosion"
#        Then the guzzle status code should be 200
#
#    @resetFixture
#    Scenario: Search All Tags by type
#        Given that I want to get all "Tags"
#        And that the request "query string" is:
#            """
#            type=category
#            """
#        When I request "/tags"
#        Then the response is JSON
#        And the "count" property equals "9"
#        Then the guzzle status code should be 200
#
#    @resetFixture
#    Scenario: Search All Tags by parent
#        Given that I want to get all "Tags"
#        And that the request "query string" is:
#            """
#            parent_id=3
#            """
#        When I request "/tags"
#        Then the response is JSON
#        And the "count" property equals "1"
#        Then the guzzle status code should be 200
#
    Scenario: Finding a Tag
        Given that I want to find a "Category"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        And that its "id" is "1"
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Tag
        Given that I want to find a "Category"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v4"
        And that its "id" is "1333"
        When I request "/categories"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Tag
        Given that I want to delete a "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v4"
        And that its "id" is "1"
        When I request "/categories"
        Then the guzzle status code should be 200

#    @resetFixture
#    Scenario: Deleting a tag removes it from attribute options
#        Given that I want to delete a "Tag"
#        And that its "id" is "1"
#        When I request "/tags"
#        Then the guzzle status code should be 200
#        Given that I want to find a "Attribute"
#        And that its "id" is "26"
#        When I request "/forms/1/attributes"
#        Then the response is JSON
#        And the response has an "options" property
#        And the "options" property does not contain "1"
#        Then the guzzle status code should be 200
#
#    Scenario: Deleting a non-existent Tag
#        Given that I want to delete a "Tag"
#        And that its "id" is "35"
#        When I request "/tags"
#        And the response has a "errors" property
#        Then the guzzle status code should be 404
#
#    Scenario: Creating a new child for a tag with role=admin
#        Given that I want to make a new "Tag"
#        And that the request "data" is:
#            """
#            {
#                "parent_id":9,
#                "tag":"Valid child",
#                "slug":"valid-child",
#                "description":"I am a valid tag",
#                "type":"category",
#                "priority":1,
#                "color":"00ff00",
#                "role": "admin"
#            }
#            """
#        When I request "/tags"
#        Then the response is JSON
#        And the response has a "id" property
#        And the type of the "id" property is "numeric"
#        And the "tag" property equals "Valid child"
#        And the "slug" property equals "valid-child"
#        And the "description" property equals "I am a valid tag"
#        And the "color" property equals "#00ff00"
#        And the "priority" property equals "1"
#        And the "type" property equals "category"
#        And the response has a "role" property
#        And the type of the "role" property is "array"
#        And the "parent.id" property equals "9"
#        Then the guzzle status code should be 200
#
#    Scenario: Creating a new invalid child for a tag with role=admin
#        Given that I want to make a new "Tag"
#        And that the request "data" is:
#            """
#            {
#                "parent_id":9,
#                "tag":"Not a valid tag role",
#                "slug":"not-valid-tag-role",
#                "description":"My role is invalid",
#                "type":"category",
#                "priority":1,
#                "color":"00ff00",
#                "role":"nope"
#            }
#            """
#        When I request "/tags"
#        Then the response is JSON
#        And the response has a "errors" property
#        And the "errors.1.message" property contains "Role nope does not exist"
#        Then the guzzle status code should be 422
#
#    Scenario: Creating a new child with no role for a tag with role=admin
#        Given that I want to make a new "Tag"
#        And that the request "data" is:
#            """
#            {
#                "parent_id":9,
#                "tag":"Not a valid tag role",
#                "slug":"not-valid-tag-role",
#                "description":"My role is invalid",
#                "type":"category",
#                "priority":1,
#                "color":"00ff00",
#                "role":null
#            }
#            """
#        When I request "/tags"
#        Then the response is JSON
#        And the response has a "errors" property
#        And the "errors.1.message" property contains "Role must match the parent category"
#        Then the guzzle status code should be 422
#
#    @resetFixture
#    Scenario: Creating a new invalid child for a tag with role=admin
#        Given that I want to make a new "Tag"
#        And that the request "data" is:
#            """
#            {
#                "parent_id":9,
#                "tag":"Not a valid tag role",
#                "slug":"also-not-valid-tag-role",
#                "description":"My role is invalid",
#                "type":"category",
#                "priority":1,
#                "color":"00ff00",
#                "role":"user"
#            }
#            """
#        When I request "/tags"
#        Then the response is JSON
#        And the response has a "errors" property
#        And the "errors.1.message" property equals "Role must match the parent category"
#        Then the guzzle status code should be 422
#
#    Scenario: Updating a child tag to a different role from its parent should fail
#        Given that I want to update a "Tag"
#        And that the request "data" is:
#            """
#            {
#                "tag":"Child 2",
#                "slug":"child-2",
#                "type":"category",
#                "role":"user"
#            }
#            """
#        And that its "id" is "11"
#        When I request "/tags"
#        Then the response is JSON
#        And the response has a "errors" property
#        And the "errors.1.message" property equals "Role must match the parent category"
#        Then the guzzle status code should be 422
#
