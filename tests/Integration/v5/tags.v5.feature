@tagsFixture @rolesEnabled
Feature: Testing the Categories API
    Scenario: Creating a new Tag with a base language
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
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
                "base_language": "en",
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
        And the "result.enabled_languages.default" property equals "en"
        And the response has a "result.role" property
        And the "result.parent.id" property equals "1"
        Then the guzzle status code should be 201
    Scenario: Creating a new Tag with a base language and translation
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        And that the request "data" is:
            """
            {
                "parent_id":1,
                "tag":"Boxes with a translation",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "base_language": "en",
                "role": ["admin", "user"],
                "translations": {
                    "es": {
                        "tag": "Cajas"
                    }
                }
            }
            """
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the "result.tag" property equals "Boxes with a translation"
        And the "result.slug" property equals "boxes-with-a-translation"
        And the "result.description" property equals "Is this a box? Awesome"
        And the "result.color" property equals "#00ff00"
        And the "result.priority" property equals "1"
        And the "result.type" property equals "category"
        And the "result.enabled_languages.default" property equals "en"
        And the "result.enabled_languages.available.0" property equals "es"
        And the response has a "result.role" property
        And the "result.parent.id" property equals "1"
        Then the guzzle status code should be 201
    Scenario: Listing Tag 1 and checking children have translations
        Given that I want to find a "Category"
        And that its "id" is "1"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        When I request "/categories"
        Then the response is JSON
        And the "result.children.1.translations.es.tag" property equals "Cajas"
        Then the guzzle status code should be 200
    Scenario: Creating a duplicate tag
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
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
        And the response has a "messages.tag" property
        And the "messages.tag.0" property equals "Tag must be unique"
        Then the guzzle status code should be 422
    Scenario: Creating a child tag with the wrong role for its parent
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        And that the request "data" is:
            """
            {
                "parent_id":1,
                "tag":"Boxes wrong role",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role": ["user"]
            }
            """
        When I request "/categories"
        Then the response is JSON
        And the response has a "messages.role" property
        And the "messages.role.0" property equals "The child category role must be the same as the parent role."
        Then the guzzle status code should be 422
    Scenario: Creating a tag with a duplicate slug is not possible
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        And that the request "data" is:
            """
            {
                "parent_id":1,
                "tag":"My boxes",
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
        And the response has a "result.slug" property
        And the "result.slug" property equals "my-boxes"
        Then the guzzle status code should be 201
    Scenario: Creating a tag with a long name fails
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
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
        And the response has a "messages.tag" property
        And the "messages.tag.0" property equals "Tag must not exceed 255 characters long"
        Then the guzzle status code should be 422

    Scenario: Check slug is generated on new tag
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
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
        And that the api_url is "api/v5"
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
        And that the api_url is "api/v5"
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
        And the response has a "messages.parent_id" property
        And the "messages.parent_id.0" property equals "Parent category must exist"
        Then the guzzle status code should be 422

    Scenario: Creating a tag with no parent_id works
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        And that the request "data" is:
            """
            {
                "tag":"I expect tags to be here",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"#00ff00",
                "role": ["admin", "user"]
            }
            """
        When I request "/categories"
        Then the response is JSON
        Then the guzzle status code should be 201

    Scenario: Creating a tag with empty parent_id works
        Given that I want to make a new "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        And that the request "data" is:
            """
            {
                "parent_id": null,
                "tag":"I expect a tag to be here",
                "description":"Is this a box? Awesome",
                "type":"category",
                "priority":1,
                "color":"#00ff00",
                "role": ["admin", "user"]
            }
            """
        When I request "/categories"
        Then the response is JSON
        Then the guzzle status code should be 201
    Scenario: Updating a Tag
        Given that I want to update a "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
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
        And that the api_url is "api/v5"
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
        And the response has a "error" property
        Then the guzzle status code should be 404

    @resetFixture
    Scenario: Updating Tag Role Restrictions
        Given that I want to update a "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
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
        And that the api_url is "api/v5"
        And that the request "data" is:
            """
            {
                "tag":"Change Role",
                "slug":"change-role",
                "type":"status",
                "role":null
            }
            """
        And that its "id" is "1"
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the "result.id" property equals "1"
        And the "result.role" property is empty
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing All Tags available to admins
        Given that I want to get all "Categories"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        When I request "/categories"
        Then the response is JSON
        And the "results" property count is "19"
        Then the guzzle status code should be 200
    Scenario: Listing All Tags available to regular users
        Given that I want to get all "Categories"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v5"
        When I request "/categories"
        Then the response is JSON
        And the "results" property count is "11"
        Then the guzzle status code should be 200
    Scenario: Listing All Tags available to non-users
        Given that I want to get all "Categories"
        And that the api_url is "api/v5"
        When I request "/categories"
        Then the response is JSON
        And the "results" property count is "10"
        Then the guzzle status code should be 200
#
#    @resetFixture
#    Scenario: Search All Tags
#        Given that I want to get all "Categories"
#        And that the request "query string" is:
#            """
#            q=Broken
#            """
#        When I request "/categories"
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
#        When I request "/categories"
#        Then the response is JSON
#        And the "count" property equals "9"
#        Then the guzzle status code should be 200
#
#    @resetFixture
#    Scenario: Search All Categories by parent [expect 0]
#        Given that I want to get all "Categories"
#        And that the oauth token is "testbasicuser"
#        And that the api_url is "api/v5"
#        And that the request "query string" is:
#            """
#            parent_id=16
#            """
#        When I request "/categories"
#        Then the response is JSON
#        And the "count" property equals "0"
#        Then the guzzle status code should be 200
#    Scenario: Search All Categories by parent [expect 1]
#        Given that I want to get all "Categories"
#        And that the oauth token is "testbasicuser"
#        And that the api_url is "api/v5"
#        And that the request "query string" is:
#            """
#            parent_id=1
#            """
#        When I request "/categories"
#        Then the response is JSON
#        And the "count" property equals "1"
#        Then the guzzle status code should be 200
    Scenario: Finding a Tag
        Given that I want to find a "Category"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v5"
        And that its "id" is "1"
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Tag
        Given that I want to find a "Category"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v5"
        And that its "id" is "1333"
        When I request "/categories"
        Then the response is JSON
        And the response has a "error" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Tag
        Given that I want to delete a "Category"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        And that its "id" is "1"
        When I request "/categories"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Deleting a tag removes it from attribute options
        Given that I want to find a "Survey"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that its "id" is "10"
        When I request "/surveys"
        Then the response is JSON
        And the "result.tasks.0.fields.0.options.0.id" property equals "12"
        And the "result.tasks.0.fields.0.options.6.id" property equals "18"
        And the "result.tasks.0.fields.0.options.6.children.0.id" property equals "19"
        Then the guzzle status code should be 200
        Given that I want to delete a "Category"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that its "id" is "12"
        When I request "/categories"
        Then the guzzle status code should be 200
        Given that I want to find a "Survey"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that its "id" is "10"
        When I request "/surveys"
        Then the response is JSON
        And the "result.tasks.0.fields.0.options.0.id" property equals "13"
        And the "result.tasks.0.fields.0.options.5.id" property equals "18"
        And the "result.tasks.0.fields.0.options.5.children.0.id" property equals "19"
        Then the guzzle status code should be 200

    Scenario: Deleting a non-existent Category
        Given that I want to delete a "Category"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that its "id" is "353"
        When I request "/categories"
        And the response has a "error" property
        Then the guzzle status code should be 404

    Scenario: Creating a new child for a tag with role=null
        Given that I want to make a new "Category"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
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
                "role": null
            }
            """
        When I request "/categories"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the "result.tag" property equals "Valid child"
        And the "result.slug" property equals "valid-child"
        And the "result.description" property equals "I am a valid tag"
        And the "result.color" property equals "#00ff00"
        And the "result.priority" property equals "1"
        And the "result.type" property equals "category"
        And the response does not have a "result.role" property
        And the "result.parent.id" property equals "9"
        Then the guzzle status code should be 201


    Scenario: Creating a new invalid child for a tag with role=admin
        Given that I want to make a new "Category"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
        """
            {
                "parent_id":16,
                "tag":"Valid child",
                "slug":"valid-child",
                "description":"I am a valid tag",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role": "interesting"
            }
        """
        When I request "/categories"
        Then the response is JSON
        And the response has a "error" property
        And the "messages.role.0" property contains "The child category role must be the same as the parent role."
        Then the guzzle status code should be 422

    Scenario: Creating a new child with no role for a tag with role=["admin"]
        Given that I want to make a new "Category"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
        """
            {
                "parent_id":16,
                "tag":"Valid childs",
                "slug":"valid-childs",
                "description":"I am a valid tag",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role": "null"
            }
        """
        When I request "/categories"
        Then the response is JSON
        And the "messages.role.0" property equals "The child category role must be the same as the parent role."
        Then the guzzle status code should be 422

    @resetFixture
    Scenario: Creating a new invalid child for a tag with role=admin
        Given that I want to make a new "Category"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {
                "parent_id":16,
                "tag":"Not a valid tag role",
                "slug":"also-not-valid-tag-role",
                "description":"My role is invalid",
                "type":"category",
                "priority":1,
                "color":"00ff00",
                "role":"user"
            }
            """
        When I request "/categories"
        Then the response is JSON
        And the response has a "error" property
        And the "messages.role.0" property equals "The child category role must be the same as the parent role."
        Then the guzzle status code should be 422

    Scenario: Updating a child tag to a different role from its parent should fail
        Given that I want to update a "Category"
        And that the api_url is "api/v5"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
            """
            {
                "tag":"Child 2",
                "slug":"child-2",
                "type":"category",
                "role":"['user']"
            }
            """
        And that its "id" is "11"
        When I request "/categories"
        Then the response is JSON
        And the response has a "error" property
        And the "messages.role.0" property equals "The child category role must be the same as the parent role."
        Then the guzzle status code should be 422

