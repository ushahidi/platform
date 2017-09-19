@acl
Feature: Access Control when modifying posts

    @rolesEnabled
    Scenario: Users can edit their own posts
        Given that I want to update a "Post"
        And that its "id" is "110"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that the request "data" is:
        """
        {
            "form_id": 1,
            "type": "report",
            "title": "Test editing own post",
            "content": "testing post for oauth",
            "status": "published",
            "locale": "en_us"
        }
        """
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has an "id" property

    @rolesEnabled
    Scenario: Users without 'Edit own Posts' canNOT edit their own posts
        Given that I want to update a "Post"
        And that its "id" is "200"
        And that the request "Authorization" header is "Bearer testnoedit"
        And that the request "data" is:
        """
        {
            "form_id": 1,
            "type": "report",
            "title": "Test editing own post",
            "content": "testing post for oauth",
            "status": "published",
            "locale": "en_us"
        }
        """
        When I request "/posts"
        Then the guzzle status code should be 403
        And the response is JSON

    @rolesEnabled
    Scenario: Users trying to update another post by change ownership to themselves should fail
        Given that I want to update a "Post"
        And that its "id" is "105"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that the request "data" is:
        """
        {
            "form_id": 1,
            "type": "report",
            "title": "Test hacking post",
            "content": "testing hacking post",
            "status": "published",
            "locale": "en_us",
            "user_id": 1
        }
        """
        When I request "/posts"
        Then the guzzle status code should be 403
        And the response is JSON
        And the response has an "errors" property

    @rolesEnabled
    Scenario: Anonymous users can not edit posts
        Given that I want to update a "Post"
        And that the request "Authorization" header is "Bearer testanon"
        And that its "id" is "110"
        And that the request "data" is:
        """
        {
            "form_id": 1,
            "title": "Test post",
            "content": "testing post for oauth",
            "status": "published"
        }
        """
        When I request "/posts"
        Then the guzzle status code should be 403

    @rolesEnabled
    Scenario: Users can delete their own posts
        Given that I want to delete a "Post"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that its "id" is "110"
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        Then the guzzle status code should be 200

    @rolesEnabled
    Scenario: Users without 'Edit own Posts' canNOT delete their own posts
        Given that I want to delete a "Post"
        And that the request "Authorization" header is "Bearer testnoedit"
        And that its "id" is "200"
        When I request "/posts"
        Then the response is JSON
        Then the guzzle status code should be 403

    @rolesEnabled
    Scenario: Users without 'Edit own Posts' can view their own posts
        Given that I want to find a "Post"
        And that its "id" is "200"
        And that the request "Authorization" header is "Bearer testnoedit"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has an "id" property

    @rolesDisabled
    Scenario: Users can edit their own posts w/ custom roles disabled
        Given that I want to update a "Post"
        And that its "id" is "111"
        And that the request "Authorization" header is "Bearer testbasicuser"
        And that the request "data" is:
        """
        {
            "form_id": 1,
            "type": "report",
            "title": "Test editing own post",
            "content": "testing post for oauth",
            "status": "published",
            "locale": "en_us"
        }
        """
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has an "id" property

    @rolesDisabled
    Scenario: Custom role cannot edit their own posts w/ custom roles disabled
        Given that I want to update a "Post"
        And that its "id" is "200"
        And that the request "Authorization" header is "Bearer testnoedit"
        And that the request "data" is:
        """
        {
            "form_id": 1,
            "type": "report",
            "title": "Test editing own post",
            "content": "testing post for oauth",
            "status": "published",
            "locale": "en_us"
        }
        """
        When I request "/posts"
        Then the guzzle status code should be 403
        And the response is JSON
