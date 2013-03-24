@post
Feature: Testing the Posts API

    Scenario: Creating a new Post
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form":1,
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
                },
                "tags":["missing"]
            }
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "title" property
        And the "title" property equals "Test post"
        And the "tags" property contains "missing"
        Then the response status code should be 200

    Scenario: Creating an invalid Post
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form":1,
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
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the "tags" property contains "kenyan"
        And the response has a "title" property
        And the "title" property equals "Updated Test Post"
        Then the response status code should be 200

    Scenario: Updating a non-existent Post
        Given that I want to update a "Post"
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
        And that its "id" is "40"
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 404

    Scenario: Updating a Post with partial data
        Given that I want to update a "Post"
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
                    "status":"believed_missing"
                },
                "tags":["missing","kenyan"]
            }
            """
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "values.last_location" property
        Then the response status code should be 200

    Scenario: Updating a Post with non-existent Form
        Given that I want to update a "Post"
        And that the request "data" is:
            """
            {
                "form":35,
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
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 400

    @searchPostFixture
    Scenario: Listing All Posts
        Given that I want to get all "Posts"
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "4"
        Then the response status code should be 200

    @searchPostFixture
    Scenario: Listing All Posts with limit and offset
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            limit=1&offset=1
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        And the response has a "next" property
        And the response has a "prev" property
        And the response has a "curr" property
        And the "results.0.id" property equals "97"
        Then the response status code should be 200

    @searchPostFixture
    Scenario: Listing All Posts with different sort
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            orderby=id&order=desc
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "results.0.id" property equals "99"
        Then the response status code should be 200

    # @todo improve this test to check more response data
    Scenario: Listing All Posts as GeoJSON
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            format=geojson
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "type" property
        And the response has a "features" property
        Then the response status code should be 200

    # @todo improve this test to check more response data
    Scenario: Listing All Posts as JSONP
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            format=jsonp&callback=parseResponse
            """
        When I request "/posts"
        Then the response is JSONP
        Then the response status code should be 200

    @searchPostFixture
    Scenario: Search All Posts
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            q=Searching&type=report
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the response status code should be 200

    @searchPostFixture
    Scenario: Search All Posts by attribute
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            dummy_varchar=special-string
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the response status code should be 200

    Scenario: Finding a Post
        Given that I want to find a "Post"
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the response status code should be 200

    Scenario: Finding a non-existent Post
        Given that I want to find a "Post"
        And that its "id" is "35"
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 404

    Scenario: Deleting a Post
        Given that I want to delete a "Post"
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        Then the response status code should be 200

    Scenario: Fail to delete a non existent Post
        Given that I want to delete a "Post"
        And that its "id" is "35"
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the response status code should be 404