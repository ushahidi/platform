@post @oauth2Skip
Feature: Testing the Posts API

    Scenario: Creating a new Post
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form":1,
                "title":"Test post",
                "user":{
                  "first_name": "Robbie",
                  "last_name": "Mackay",
                  "email": "someotherrobbie@test.com"
                },
                "type":"report",
                "status":"draft",
                "locale":"en_US",
                "values":
                {
                    "full_name":"David Kobia",
                    "description":"Skinny, homeless Kenyan last seen in the vicinity of the greyhound station",
		    "date_of_birth":null,
                    "missing_date":"2012/09/25",
                    "last_location":"atlanta",
                    "last_location_point":{
                      "lat":33.755,
                      "lon":-84.39
                    },
                    "geometry_test":"POLYGON((0 0,1 1,2 2,0 0))",
                    "missing_status":"believed_missing",
                    "links":[
                      {"value":"http://google.com"},
                      {"value":"http://facebook.com"}
                    ]
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
        And the response has a "tags.0.id" property
        And the "values.last_location_point.lat" property equals "33.755"
        And the "values.geometry_test" property equals "POLYGON((0 0,1 1,2 2,0 0))"
        And the "values.links.0.value" property equals "http://google.com"
        And the response has a "values.links.0.id" property
        Then the guzzle status code should be 200

    Scenario: Creating an Post with invalid data returns an error
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form":1,
                "title":"Invalid post",
                "type":"report",
                "status":"draft",
                "locale":"en_US",
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
        Then the guzzle status code should be 400

    Scenario: Creating an Post without required fields returns an error
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form":1,
                "title":"Invalid post",
                "type":"report",
                "status":"draft",
                "locale":"en_US",
                "values":
                {
                    "full_name":"David Kobia",
                    "description":"Skinny, homeless Kenyan last seen in the vicinity of the greyhound station",
		    "date_of_birth":null,
                    "missing_date":"2012/09/25",
                    "missing_status":"believed_missing"
                }
            }
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 400

    Scenario: Creating an Post with existing user returns an error
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form":1,
                "title":"Invalid author",
                "type":"report",
                "status":"draft",
                "locale":"en_US",
                "user":{
                  "first_name": "Robbie",
                  "last_name": "Mackay",
                  "email": "robbie@ushahidi.com"
                },
                "values":
                {
                    "full_name":"David Kobia",
                    "description":"Skinny, homeless Kenyan last seen in the vicinity of the greyhound station",
		    "date_of_birth":null,
                    "missing_date":"2012/09/25",
                    "status":"believed_missing",
                    "last_location":"atlanta"
                }
            }
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 400

    Scenario: Creating a Post with existing user by ID
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form":1,
                "title":"Invalid author",
                "type":"report",
                "status":"draft",
                "locale":"en_US",
                "user":{
                  "id": 1
                },
                "values":
                {
                    "full_name":"David Kobia",
                    "description":"Skinny, homeless Kenyan last seen in the vicinity of the greyhound station",
		    "date_of_birth":null,
                    "missing_date":"2012/09/25",
                    "missing_status":"believed_missing",
                    "last_location":"atlanta"
                }
            }
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        And the "user.id" property equals "1"
        Then the guzzle status code should be 200

    Scenario: Updating a Post
        Given that I want to update a "Post"
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
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1"
        And the response has a "tags.1.id" property
        And the response has a "title" property
        And the "title" property equals "Updated Test Post"
        And the "values.last_location_point.lon" property equals "-85.39"
        Then the guzzle status code should be 200

    Scenario: Updating a non-existent Post
        Given that I want to update a "Post"
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
                    "missing_status":"believed_missing"
                },
                "tags":["missing","kenyan"]
            }
            """
        And that its "id" is "40"
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Updating a Post with partial data
        Given that I want to update a "Post"
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
                    "last_location":"atlanta"
                },
                "tags":["missing","kenyan"]
            }
            """
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response does not have a "values.missing_status" property
        Then the guzzle status code should be 200

    Scenario: Updating a Post with non-existent Form
        Given that I want to update a "Post"
        And that the request "data" is:
            """
            {
                "form":35,
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
                    "missing_status":"believed_missing"
                },
                "tags":["missing","kenyan"]
            }
            """
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 400

    @resetFixture
    Scenario: Listing All Posts
        Given that I want to get all "Posts"
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "11"
        Then the guzzle status code should be 200

    @resetFixture
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
        And the "results.0.id" property equals "9999"
        Then the guzzle status code should be 200

    @resetFixture
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
        And the "results.0.id" property equals "9999"
        Then the guzzle status code should be 200

    # @todo improve this test to check more response data
    Scenario: Listing All Posts as JSONP
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            format=jsonp&callback=parseResponse
            """
        When I request "/posts"
        Then the response is JSONP
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Posts
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            q=Searching
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Posts by locale
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            locale=fr_FR
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    # Regression test to ensure q= filter can be used with other filters
    @resetFixture
    Scenario: Search All Posts with query and locale
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            q=Searching&locale=fr_FR
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Posts by attribute
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            test_varchar=special
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Posts by single tag
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            tags=4
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search for posts with tag 3 AND 4
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            tags[all]=3,4
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search for posts with tag 3 OR 4
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            tags[any]=3,4
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    Scenario: Finding a Post
        Given that I want to find a "Post"
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Post
        Given that I want to find a "Post"
        And that its "id" is "35"
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Post
        Given that I want to delete a "Post"
        And that its "id" is "1"
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        Then the guzzle status code should be 200

    Scenario: Fail to delete a non existent Post
        Given that I want to delete a "Post"
        And that its "id" is "35"
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Creating a new Post with UTF-8 title
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "form":1,
                "title":"SUMMARY REPORT (تقرير ملخص)",
                "author":"robbie",
                "email":"robbie@ushahidi.com",
                "type":"report",
                "status":"draft",
                "locale":"en_US",
                "values":
                {
                    "full_name":"David Kobia",
                    "description":"Skinny, homeless Kenyan last seen in the vicinity of the greyhound station",
		    "date_of_birth":null,
                    "missing_date":"2012/09/25",
                    "last_location":"atlanta",
                    "missing_status":"believed_missing"
                },
                "tags":["missing"]
            }
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "title" property
        And the "title" property equals "SUMMARY REPORT (تقرير ملخص)"
        And the "slug" property equals "summary-report-تقرير-ملخص"
        And the response has a "tags.0.id" property
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Search All Posts by link attribute
        Given that I want to get all "Posts"
        And that the request "query string" is:
            """
            links=http://google.com
            """
        When I request "/posts"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        Then the guzzle status code should be 200
