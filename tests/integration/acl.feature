@acl
Feature: API Access Control Layer

    Scenario: Anonymous users can create posts
        Given that I want to make a new "Post"
        And that the request "Authorization" header is "Bearer testanon"
        And that the request "data" is:
        """
        {
            "form_id": 1,
            "status": "draft",
            "title": "Test creating anonymous post",
            "content": "testing post for oauth",
            "locale": "en_us",
            "values": {
                "last_location" : ["Somewhere"]
            }
        }
        """
        When I request "/posts"
        Then the guzzle status code should be 204

    Scenario: Anonymous user can not see restricted fields of public posts
        Given that I want to find a "Post"
        And that its "id" is "121"
        When I request "/posts"
        Then the response is JSON
        And the response has a "values" property
        And the response has a "values.test_field_locking_visible_4" property
        And the response does not have a "values.test_field_locking_hidden_3" property
        Then the guzzle status code should be 200

    Scenario: Anonymous user can not see hidden tasks of public posts
        Given that I want to find a "Post"
        And that its "id" is "121"
        When I request "/posts"
        Then the response is JSON
        And the response has a "values" property
        And the response has a "values.test_field_locking_visible_4" property
        And the response does not have a "values.test_field_locking_visible_2" property
        Then the guzzle status code should be 200

    #FIXME
    Scenario: Anonymous user can not see hidden author field of public posts
        Given that I want to find a "Post"
        And that its "id" is "121"
        When I request "/posts"
        Then the response is JSON
        And the response does not have a "author_realname" property
        And the response does not have a "author_email" property
        And the response does not have a "user_id" property
        Then the guzzle status code should be 200

    Scenario: User can not see hidden tasks of public posts
        Given that I want to find a "Post"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "121"
        When I request "/posts"
        Then the response is JSON
        And the response has a "values" property
        And the response has a "values.test_field_locking_visible_4" property
        And the response does not have a "values.test_field_locking_visible_2" property
        Then the guzzle status code should be 200

    Scenario: User can not see restricted fields of public posts
        Given that I want to find a "Post"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "121"
        When I request "/posts"
        Then the response is JSON
        And the response has a "values" property
        And the response has a "values.test_field_locking_visible_4" property
        And the response does not have a "values.test_field_locking_hidden_3" property
        Then the guzzle status code should be 200

    #FIXME
    Scenario: User can not see hidden author field of public posts
        Given that I want to find a "Post"
        And that its "id" is "121"
        When I request "/posts"
        Then the response is JSON
        And the response does not have a "author_realname" property
        And the response does not have a "author_email" property
        And the response does not have a "user" property
        Then the guzzle status code should be 200

    Scenario: User can see restricted fields of posts published to their role when survey restricted to their role
        Given that I want to find a "Post"
        And that the request "Authorization" header is "Bearer testmanager"
        And that its "id" is "121"
        When I request "/posts"
        Then the response is JSON
        And the response has a "values" property
        And the response has a "values.test_field_locking_visible_4" property
        And the response has a "values.test_field_locking_hidden_3" property
        Then the guzzle status code should be 200

    Scenario: User can see hidden author field of posts published to their role when survey restricted to their role
        Given that I want to find a "Post"
        And that the request "Authorization" header is "Bearer testmanager"
        And that its "id" is "121"
        When I request "/posts"
        Then the response is JSON
        And the response has a "author_realname" property
        And the response has a "author_email" property
        And the response has a "user" property
        Then the guzzle status code should be 200

    Scenario: User can not see hidden tasks of posts published to their role
        Given that I want to find a "Post"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "122"
        When I request "/posts"
        Then the response is JSON
        And the response has a "values" property
        And the response has a "values.test_field_locking_visible_4" property
        And the response does not have a "values.test_field_locking_visible_2" property
        Then the guzzle status code should be 200

    Scenario: User can see restricted fields of posts published to their role
        Given that I want to find a "Post"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "122"
        When I request "/posts"
        Then the response is JSON
        And the response has a "values" property
        And the response has a "values.test_field_locking_visible_4" property
        And the response does not have a "values.test_field_locking_hidden_3" property
        Then the guzzle status code should be 200

    Scenario: User can not see hidden author field of posts published to their role
        Given that I want to find a "Post"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "122"
        When I request "/posts"
        Then the response is JSON
        And the response does not have a "author_realname" property
        And the response does not have a "author_email" property
        And the response does not have a "user" property
        Then the guzzle status code should be 200

    Scenario: Listing All Stages for a form with hidden stages
        Given that I want to get all "Stages"
        And that the request "Authorization" header is "Bearer testbasicuser"
        When I request "/forms/4/stages"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    Scenario: Listing All Stages for a form with hidden stages with edit permission
        Given that I want to get all "Stages"
        And that the request "Authorization" header is "Bearer testmanager"
        When I request "/forms/4/stages"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    Scenario: Listing All Attributes for a form with hidden stages
        Given that I want to get all "Stages"
        And that the request "Authorization" header is "Bearer testbasicuser"
        When I request "/forms/4/attributes"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "3"
        Then the guzzle status code should be 200

    Scenario: Listing All Attributes for a form with hidden stages with edit permission
        Given that I want to get all "Stages"
        And that the request "Authorization" header is "Bearer testmanager"
        When I request "/forms/4/attributes"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "8"
        Then the guzzle status code should be 200

    Scenario: User can see hidden tasks of posts published when survey restricted to their role
        Given that I want to find a "Post"
        And that the request "Authorization" header is "Bearer testmanager"
        And that its "id" is "121"
        When I request "/posts"
        Then the response is JSON
        And the response has a "values" property
        And the response has a "values.test_field_locking_visible_4" property
        And the response has a "values.test_field_locking_visible_2" property
        Then the guzzle status code should be 200

    Scenario: Anonymous user can access public posts
        Given that I want to get all "Posts"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the "count" property equals "12"

    Scenario: All users can view public posts
        Given that I want to get all "Posts"
        And that the request "Authorization" header is "Bearer testbasicuser2"
        And that the request "query string" is "status=all"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the "count" property equals "17"

    Scenario: User can view public and own private posts in collection
        Given that I want to get all "Posts"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that the request "query string" is "status=all"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the "count" property equals "20"

    Scenario: Admin can view all posts in collection
        Given that I want to get all "Posts"
        And that the request "Authorization" header is "Bearer testadminuser"
        And that the request "query string" is "status=all"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the "count" property equals "24"

    Scenario: Admin user can view private posts
        Given that I want to find a "Post"
        And that its "id" is "111"
        And that the request "Authorization" header is "Bearer testadminuser"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has an "id" property

    Scenario: User can view their own private posts
        Given that I want to find a "Post"
        And that its "id" is "111"
        And that the request "Authorization" header is "Bearer testbasicuser"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has an "id" property

    Scenario: Users can not view private posts
        Given that I want to find a "Post"
        And that its "id" is "111"
        And that the request "Authorization" header is "Bearer testbasicuser2"
        When I request "/posts"
        Then the guzzle status code should be 403
        And the response is JSON
        And the response has an "errors" property

    Scenario: Anonymous users can not view draft posts
        Given that I want to find a "Post"
        And that its "id" is "111"
        When I request "/posts"
        Then the guzzle status code should be 401

    Scenario: Anonymous users can view public post
        Given that I want to find a "Post"
        And that its "id" is "110"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response has an "id" property

    Scenario: Anonymous user can not access updates with private parent post
        Given that I want to get all "Updates"
        When I request "/posts/111/updates"
        Then the guzzle status code should be 401

    Scenario: Anonymous user can not access update with private parent post
        Given that I want to find an "Update"
        And that its "id" is "114"
        When I request "/posts/111/updates"
        Then the guzzle status code should be 401

    Scenario: User user can access update to their own private parent post
        Given that I want to find an "Update"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "114"
        When I request "/posts/111/updates"
        Then the guzzle status code should be 200

    Scenario: User user can not access update with private parent post
        Given that I want to find an "Update"
        And that the request "Authorization" header is "Bearer testbasicuser2"
        And that its "id" is "114"
        When I request "/posts/111/updates"
        Then the guzzle status code should be 403

    Scenario: Anonymous user can not access translations with private parent post
        Given that I want to get all "Translations"
        When I request "/posts/111/translations"
        Then the guzzle status code should be 401

    Scenario: Anonymous user can not access translation with private parent post
        Given that I want to find a "Translation"
        And that its "id" is "115"
        When I request "/posts/111/translations"
        Then the guzzle status code should be 401

    Scenario: Anonymous user can not access revisions with private parent post
        Given that I want to get all "Revisions"
        When I request "/posts/111/revisions"
        Then the guzzle status code should be 401

    Scenario: Anonymous user can not access revision with private parent post
        Given that I want to find a "Revision"
        And that its "id" is "116"
        When I request "/posts/111/revisions"
        Then the guzzle status code should be 401

    Scenario: Anonymous user can not access private update in listing
        Given that I want to get all "Updates"
        When I request "/posts/110/updates"
        Then the guzzle status code should be 200
        And the "count" property equals "0"

    Scenario: Anonymous user can not access private update
        Given that I want to find an "Update"
        And that its "id" is "117"
        When I request "/posts/110/updates"
        Then the guzzle status code should be 401

    Scenario: Anonymous users cannot edit public post
        Given that I want to update a "Post"
        And that the request "Authorization" header is "Bearer testanon"
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
                    "date_of_birth":null,
                    "missing_date":"2012/09/25",
                    "last_location":"atlanta",
                    "last_location_point":"POINT(-85.39 33.755)",
                    "missing_status":"believed_missing"
                },
                "tags":["missing","kenyan"]
            }
            """
        And that its "id" is "110"
        When I request "/posts"
        Then the guzzle status code should be 403

    Scenario: Anonymous users can view site config
        Given that I want to find a "Config"
        And that its "id" is "site"
        When I request "/config"
        Then the guzzle status code should be 200
        And the response has an "id" property
        And the "id" property equals "site"

    Scenario: Anonymous users can view map config
        Given that I want to find a "Config"
        And that its "id" is "map"
        When I request "/config"
        Then the guzzle status code should be 200
        And the response has an "id" property
        And the "id" property equals "map"

    Scenario: Anonymous user can not access data provider config
        Given that I want to find an "Update"
        And that its "id" is "data-provider"
        When I request "/config"
        Then the guzzle status code should be 401

    @resetFixture
    Scenario: Listing All Configs as admin
        Given that I want to get all "Configs"
        And that the request "Authorization" header is "Bearer testadminuser"
        When I request "/config"
        Then the response is JSON
        And the "count" property equals "6"
        Then the guzzle status code should be 200

    Scenario: Listing All Configs as anonymous user
        Given that I want to get all "Configs"
        When I request "/config"
        Then the response is JSON
        And the "count" property equals "3"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Basic user cannot access admin-only tag
        Given that I want to find a "Tag"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "7"
        When I request "/tags"
        Then the guzzle status code should be 403

    Scenario: Admin user can access protected tag
        Given that I want to find a "Tag"
        And that the request "Authorization" header is "Bearer testadminuser"
        And that its "id" is "7"
        When I request "/tags"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has an "id" property

    @resetFixture
    Scenario: Deleting a Media that I own
        Given that I want to delete a "Media"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "1"
        When I request "/media"
        Then the response is JSON
        And the response has a "id" property
        Then the guzzle status code should be 200

    Scenario: Fail to delete a Media that I do not own
        Given that I want to delete a "Media"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "2"
        When I request "/media"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 403

    Scenario: Fail to delete a Media that is anonymous
        Given that I want to delete a "Media"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "2"
        When I request "/media"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 403

    Scenario: Deleting an anonymous Media with admin
        Given that I want to delete a "Media"
        And that the request "Authorization" header is "Bearer testadminuser"
        And that its "id" is "2"
        When I request "/media"
        Then the response is JSON
        And the response has a "id" property
        Then the guzzle status code should be 200

    Scenario: Fail to access resources without corresponding scope
        Given that I want to find a "User"
        And that the request "Authorization" header is "Bearer testingtoken"
        And that its "id" is "1"
        When I request "/users"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 400

    @resetFixture
    Scenario: User can view post published to members
        Given that I want to find a "Post"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "120"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the "id" property equals "120"

    @resetFixture
    Scenario: Anonymous can not view private responses
        Given that I want to find a "Post"
        And that its "id" is "121"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the "id" property equals "121"

    @resetFixture
    Scenario: Anonymous can not view post published to members
        Given that I want to find a "Post"
        And that its "id" is "120"
        When I request "/posts"
        Then the guzzle status code should be 401
        And the response is JSON

    @private
    Scenario: Registering as a user when a deployment is private
        Given that I want to make a new "user"
        And that the request "Authorization" header is "Bearer testanon"
        And that the request "data" is:
        """
        {
            "email":"john@ushahidi.com",
            "realname":"John Tae",
            "password":"testing",
            "role":"admin"
        }
        """
        When I request "/users"
        Then the response is JSON
        Then the guzzle status code should be 403

    @private
    Scenario: Anonymous user cannot access public posts when deployment is private
        Given that I want to get all "Posts"
        When I request "/posts"
        Then the guzzle status code should be 401

    @private
    Scenario: Anonymous users cannot create posts when a deployment is private
        Given that I want to make a new "Post"
        And that the request "Authorization" header is "Bearer testanon"
        And that the request "data" is:
        """
        {
            "form_id": 1,
            "status": "draft",
            "title": "Test creating anonymous post",
            "content": "testing post for oauth",
            "locale": "en_us",
            "values": {
                "last_location" : ["Somewhere"]
            }
        }
        """
        When I request "/posts"
        Then the guzzle status code should be 403

    @private
    Scenario: Anonymous users cannot view public posts when a deployment is private
        Given that I want to find a "Post"
        And that its "id" is "110"
        When I request "/posts"
        Then the guzzle status code should be 401

    @rolesEnabled
    Scenario: User with Manage Posts permission can view all posts in collection
        Given that I want to get all "Posts"
        And that the request "Authorization" header is "Bearer testmanager"
        And that the request "query string" is "status=all"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the "count" property equals "23"

    @rolesEnabled
    Scenario: User with Manage Posts permission can view private posts
        Given that I want to find a "Post"
        And that its "id" is "111"
        And that the request "Authorization" header is "Bearer testmanager"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has an "id" property

    @rolesEnabled
    Scenario: User with Manage Posts permission is not allowed to make a SavedSearch featured
        Given that I want to update a "SavedSearch"
        And that the request "Authorization" header is "Bearer testmanager"
        And that the request "data" is:
        """
        {
            "name":"Updated Search One",
            "filter":"updated search filter",
            "featured":1
        }
        """
        And that its "id" is "5"
        When I request "/savedsearches"
        Then the response is JSON
        Then the guzzle status code should be 403

    @rolesEnabled
    Scenario: User with with Manage Settings permission can update a config
        Given that I want to update a "Config"
        And that the request "Authorization" header is "Bearer testmanager"
        And that the request "data" is:
            """
            {
                "testkey":"i am a teapot?"
            }
            """
        When I request "/config/test"
        Then the response is JSON
        And the "id" property equals "test"
        And the "testkey" property equals "i am a teapot?"
        Then the guzzle status code should be 200

    @rolesEnabled
    Scenario: User with Manage Settings permissions can list Data Providers
        Given that I want to get all "Dataproviders"
        And that the request "Authorization" header is "Bearer testmanager"
        When I request "/dataproviders"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "6"
        Then the guzzle status code should be 200

    @rolesEnabled
    Scenario: User with Manage Settings permissions can see all Tags
        Given that I want to get all "Tags"
        And that the request "Authorization" header is "Bearer testmanager"
        When I request "/tags"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "11"
        Then the guzzle status code should be 200

    @rolesEnabled
    Scenario: User with Manage Settings permission can create a new form
        Given that I want to make a new "Form"
        And that the request "Authorization" header is "Bearer testmanager"
        And that the request "data" is:
            """
            {
                "name":"Test Form",
                "type":"report",
                "description":"This is an ACL test form",
                "disabled":false
            }
            """
        When I request "/forms"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "disabled" property is false
        Then the guzzle status code should be 200

    @rolesEnabled
    Scenario: User with Manage Users permission can create a user
        Given that I want to make a new "user"
        And that the request "Authorization" header is "Bearer testmanager"
        And that the request "data" is:
        """
        {
            "email":"acluser@ushahidi.com",
            "realname":"Acl User",
            "password":"testing",
            "role":"user"
        }
        """
        When I request "/users"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "email" property
        And the "email" property equals "acluser@ushahidi.com"
        And the "role" property equals "user"
        And the response does not have a "password" property
        Then the guzzle status code should be 200

    @rolesEnabled @dataImportEnabled
    Scenario: Uploading a CSV file with the Importer role
        Given that I want to make a new "CSV"
        And that the request "Authorization" header is "Bearer testimporter"
        And that the post file "file" is "tests/datasets/ushahidi/sample.csv"
        When I request "/csv"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "columns" property
        And the "columns.0" property equals "title"
        Then the guzzle status code should be 200
