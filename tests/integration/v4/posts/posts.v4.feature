@post @rolesEnabled
Feature: Testing the Posts API
  @create @rolesEnabled
  Scenario: Creating a new Post
    Given that I want to make a new "Post"
    And that the oauth token is "testmanager"
    And that the api_url is "api/v4"
    And that the request "data" is:
			"""
			{
				"form":1,
				"title":"Test post",
				"author_realname": "Robbie Mackay",
				"author_email": "someotherrobbie@test.com",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"post_date": "2016-10-15T12:18:27+13:00",
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"last_location":["atlanta"],
					"last_location_point":[{
						"lat":33.755,
						"lon":-84.39
					}],
					"geometry_test":["POLYGON((0 0,1 1,2 2,0 0))"],
					"missing_status":["believed_missing"],
					"links":[
						"http://google.com",
						"http://facebook.com"
					],
					"tags1": [1]
				},
				"completed_stages":[1]
			}
			"""
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the response has a "result.title" property
    And the "result.title" property equals "Test post"
    And the response has a "result.tags.0.id" property
    And the "result.post_content.last_location_point.0.lat" property equals "33.755"
    And the "result.post_content.geometry_test" property contains "POLYGON((0 0,1 1,2 2,0 0))"
    And the "result.post_content.links.0" property equals "http://google.com"
    And the type of the "result.completed_stages.0" property is "int"
    And the "result.completed_stages" property contains "1"
    And the "result.post_date" property equals "2016-10-14T23:18:27+00:00"
    Then the guzzle status code should be 200
