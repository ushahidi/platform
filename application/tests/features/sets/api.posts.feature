@oauth2Skip
Feature: Testing the Sets Posts API

    Scenario: Creating a new Post in a Set
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
                    "date_of_birth":"unknown",
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
        When I request "/sets/1/posts/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200