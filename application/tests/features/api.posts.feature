@post
Feature: Testing the Posts API

    Scenario: Creating a new Post
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form_id":1,
                "title":"Test post",
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
            }
            }
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "title" property
        And the "title" property equals "Test post"
        Then the response status code should be 200

    Scenario: Creating an invalid Post
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form_id":1,
                "title":"Invalid post",
                "type":"report",
                "status":"draft",
                "values":
                {
                    "missing_field":"David Kobia",
                    "date_of_birth":"2012/33/33"
                }
            }
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 400

    Scenario: Updating a Post
        Given that I want to update a "Post"
        And that the request "data" is:
            """
            {
                "form_id":1,
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
                }
            }
            """
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the response has a "title" property
        And the "title" property equals "Updated Test Post"
        Then the response status code should be 200

    Scenario: Listing All Posts
        Given that I want to get all "Posts"
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the response status code should be 200

    Scenario: Finding a Post
        Given that I want to find a "Post"
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the response status code should be 200

    Scenario: Deleting a Post
        Given that I want to delete a "Post"
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        Then the response status code should be 200
