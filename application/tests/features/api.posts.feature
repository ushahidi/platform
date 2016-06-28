@post @oauth2Skip
Feature: Testing the Posts API

	@create
	Scenario: Creating a new Post
		Given that I want to make a new "Post"
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
					]
				},
				"tags":["explosion"],
				"completed_stages":[1]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the response has a "title" property
		And the "title" property equals "Test post"
		And the response has a "tags.0.id" property
		And the "values.last_location_point.0.lat" property equals "33.755"
		And the "values.geometry_test" property contains "POLYGON((0 0,1 1,2 2,0 0))"
		And the "values.links.0" property equals "http://google.com"
		And the "completed_stages" property contains "1"
		Then the guzzle status code should be 200

	@create
	Scenario: Creating a new Post with JSON date format
		Given that I want to make a new "Post"
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
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2016-05-31T00:00:00.000Z"],
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
					]
				},
				"tags":["explosion"],
				"completed_stages":[1]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the response has a "title" property
		And the "title" property equals "Test post"
		And the response has a "tags.0.id" property
		And the "values.missing_date.0" property equals "2016-05-31 00:00:00"
		Then the guzzle status code should be 200

	@create
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
					"missing_field":["David Kobia"],
					"date_of_birth":["2012/33/33"]
				}
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 422

	@create
	Scenario: Creating a new Post with too many values for attribute returns an error
		Given that I want to make a new "Post"
		And that the request "data" is:
			"""
			{
				"form":1,
				"title":"Test post",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"values":
				{
					"last_location":[
						"atlanta",
						"auckland"
					]
				},
				"tags":["explosion"]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 422

	@create
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
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"missing_status":["believed_missing"]
				},
				"completed_stages":[1]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 422

	@create
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
				"author_realname": "Robbie Mackay",
				"author_email": "robbie@ushahidi.com",
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"status":["believed_missing"],
					"last_location":["atlanta"]
				}
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 422

	@create
	Scenario: Creating a Post with existing user by ID (authorized as admin user)
		Given that I want to make a new "Post"
		And that the request "data" is:
			"""
			{
				"form":1,
				"title":"Author id 1",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"user":{
					"id": 1
				},
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"missing_status":["believed_missing"],
					"last_location":["atlanta"]
				}
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the "user.id" property equals "1"
		Then the guzzle status code should be 200

	@create
	Scenario: A normal user creates a Post with different user as author, should get permission error
		Given that I want to make a new "Post"
		And that the request "Authorization" header is "Bearer testbasicuser2"
		And that the request "data" is:
			"""
			{
				"form":1,
				"title":"Author id 1",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"user":{
					"id": 1
				},
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"missing_status":["believed_missing"],
					"last_location":["atlanta"]
				}
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 403

	@create
	Scenario: Creating a Post with no user gets current uid
		Given that I want to make a new "Post"
		And that the request "data" is:
			"""
			{
				"form":1,
				"title":"Invalid author",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"user":null,
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"missing_status":["believed_missing"],
					"last_location":["atlanta"]
				}
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the "user.id" property equals "2"
		Then the guzzle status code should be 200

	@update
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
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"last_location":["atlanta"],
					"last_location_point":[
						{
							"lat": 33.755,
							"lon": -85.39
						}
					],
					"missing_status":["believed_missing"]
				},
				"tags":["disaster","explosion"],
				"completed_stages":[1]
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
		And the "values.last_location_point.0.lon" property equals "-85.39"
		Then the guzzle status code should be 200

	@update
	Scenario: Updating a Post using JSON date formate
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
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2016-05-31T00:00:00.000Z"],
					"last_location":["atlanta"],
					"last_location_point":[
						{
							"lat": 33.755,
							"lon": -85.39
						}
					],
					"missing_status":["believed_missing"]
				},
				"tags":["disaster","explosion"],
				"completed_stages":[1]
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
		And the "values.missing_date.0" property equals "2016-05-31 00:00:00"
		And the "values.last_location_point.0.lon" property equals "-85.39"
		Then the guzzle status code should be 200

	@update
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
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"missing_status":["believed_missing"],
					"last_location":["atlanta"]
				},
				"tags":["disaster","explosion"]
			}
			"""
		And that its "id" is "40"
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	@resetFixture @update
	Scenario: Updating user info on a Post (as admin)
		Given that I want to update a "Post"
		And that the request "data" is:
			"""
			{
				"form":1,
				"title":"Updated Test Post",
				"type":"report",
				"status":"published",
				"locale":"en_US",
				"user":{
					"id": 4
				},
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"last_location":["atlanta"],
					"last_location_point":[
						{
							"lat": 33.755,
							"lon": -85.39
						}
					],
					"missing_status":["believed_missing"]
				},
				"tags":["disaster","explosion"]
			}
			"""
		And that its "id" is "1"
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "id" property equals "1"
		And the "user.id" property equals "4"
		Then the guzzle status code should be 200

	@update @resetFixture
	Scenario: Updating author info on a Post (as admin)
		Given that I want to update a "Post"
		And that the request "data" is:
			"""
			{
				"form":1,
				"title":"Updated Test Post",
				"type":"report",
				"status":"published",
				"locale":"en_US",
				"author_realname": "Some User",
				"author_email": "someuser@ushahidi.com",
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"last_location":["atlanta"],
					"last_location_point":[
						{
							"lat": 33.755,
							"lon": -85.39
						}
					],
					"missing_status":["believed_missing"]
				},
				"tags":["disaster","explosion"]
			}
			"""
		And that its "id" is "1"
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "id" property equals "1"
		And the "author_realname" property equals "Some User"
		And the "author_email" property equals "someuser@ushahidi.com"
		Then the guzzle status code should be 200

	@update @resetFixture
	Scenario: Update a post with user id and author info should fail
		Given that I want to update a "Post"
		And that the request "data" is:
			"""
			{
				"form":1,
				"title":"Updated Test Post",
				"type":"report",
				"status":"published",
				"locale":"en_US",
				"user":{
					"id": 1
				},
				"author_email": "someuser@ushahidi.com",
				"author_realname": "Some User",
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"last_location":["atlanta"],
					"last_location_point":[
						{
							"lat": 33.755,
							"lon": -85.39
						}
					],
					"missing_status":["believed_missing"]
				},
				"tags":["disaster","explosion"]
			}
			"""
		And that its "id" is "1"
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 422

	@resetFixture @update
	Scenario: A normal user updating a post with a new user id should get access denied
		Given that I want to update a "Post"
		And that the request "Authorization" header is "Bearer testbasicuser"
		And that the request "data" is:
			"""
			{
				"form":1,
				"title":"Updated Test Post",
				"type":"report",
				"status":"published",
				"locale":"en_US",
				"user":{
					"id": 4
				},
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"last_location":["atlanta"],
					"last_location_point":[
						{
							"lat": 33.755,
							"lon": -85.39
						}
					],
					"missing_status":["believed_missing"]
				},
				"tags":["disaster","explosion"]
			}
			"""
		And that its "id" is "110"
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 403

	@resetFixture @update
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
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"last_location":["atlanta"]
				},
				"tags":["disaster","explosion"]
			}
			"""
		And that its "id" is "1"
		When I request "/posts"
		Then the response is JSON
		And the response does not have a "values.missing_status" property
		Then the guzzle status code should be 200

	@resetFixture @update
	Scenario: Extra params passed when updating a post get ignored
		Given that I want to update a "Post"
		And that the request "data" is:
			"""
			{
				"form":1,
				"url":"http://ushv3.dev/api/v2/posts/1",
				"title":"Updated Test Post",
				"type":"report",
				"status":"published",
				"locale":"en_US",
				"values":
				{
					"last_location":["atlanta"]
				},
				"tags":["disaster","explosion"],
				"categories":["something"]
			}
			"""
		And that its "id" is "1"
		When I request "/posts"
		Then the response is JSON
		And the response does not have a "values.missing_status" property
		Then the guzzle status code should be 200

	@resetFixture @create
	Scenario: Creating a Post with non-existent Form
		Given that I want to make a new "Post"
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
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"last_location":["atlanta"],
					"missing_status":["believed_missing"]
				},
				"tags":["disaster","explosion"]
			}
			"""
		And that its "id" is "1"
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 422

	@resetFixture @search
	Scenario: Listing All Posts
		Given that I want to get all "Posts"
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "12"
		And the "total_count" property equals "12"
		Then the guzzle status code should be 200

	@resetFixture @search
	Scenario: Listing all posts with limit 1 and offset 1 should return 1 post
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
		Then the guzzle status code should be 200

	@resetFixture @search
	Scenario: Listing posts should default to sorting by created date (asc)
		Given that I want to get all "Posts"
		When I request "/posts"
		Then the response is JSON
		And the "order" property equals "asc"
		And the "orderby" property equals "created"
		Then the guzzle status code should be 200

	@resetFixture @search
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

	@resetFixture @search
	Scenario: Listing All Posts owned by me
		Given that I want to get all "Posts"
        And that the request "Authorization" header is "Bearer testbasicuser2"
		And that the request "query string" is:
			"""
			user=me
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "1"
		And the "total_count" property equals "1"
		Then the guzzle status code should be 200

	@resetFixture @search
	Scenario: Listing All Posts w/o form
		Given that I want to get all "Posts"
		And that the request "query string" is:
			"""
			form=none
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "1"
		And the "total_count" property equals "1"
		Then the guzzle status code should be 200

	# @todo improve this test to check more response data
	@search
	Scenario: Listing All Posts as JSONP
		Given that I want to get all "Posts"
		And that the request "query string" is:
			"""
			format=jsonp&callback=parseResponse
			"""
		When I request "/posts"
		Then the response is JSONP
		Then the guzzle status code should be 200

	@resetFixture @search
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

        @resetFixture @csvexport
	Scenario: Search All Posts and export the results
		Given that I want to get all "Posts"
		And that the request "query string" is:
			"""
			q=Searching&format=csv
			"""
		When I request "/posts/export"
                And that the response "Content-Type" header is "text/csv"
		Then the guzzle status code should be 200

	@resetFixture @search
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
	@resetFixture @search
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

	@resetFixture @search
	Scenario: Search All Posts by attribute
		Given that I want to get all "Posts"
		And that the request "query string" is:
			"""
			values[test_varchar]=special
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "1"
		Then the guzzle status code should be 200

	@resetFixture @search
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

	@resetFixture @search
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

	@resetFixture @search
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

	@resetFixture @search
	Scenario: Filter All Posts by single stage
		Given that I want to get all "Posts"
		And that the request "query string" is:
			"""
			current_stage=3
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "1"
		Then the guzzle status code should be 200

	@resetFixture @search
	Scenario: Filter All Posts by multiple stages
		Given that I want to get all "Posts"
		And that the request "query string" is:
			"""
			current_stage=1,3
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "9"
		Then the guzzle status code should be 200

	@resetFixture @search
	Scenario: Filter All Posts by multiple collections
		Given that I want to get all "Posts"
		And that the request "query string" is:
			"""
			set=1,2
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "3"
		Then the guzzle status code should be 200

	@get
	Scenario: Finding a Post
		Given that I want to find a "Post"
		And that its "id" is "1"
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the response has a "url" property
		And the "title" property equals "Test post"
		And the "content" property equals "Testing post"
		And the "form.id" property equals "1"
		And the response has a "tags" property
		And the response has a "values" property
		And the "values.geometry_test" property contains "MULTIPOLYGON(((40 40,20 45,45 30,40 40)),((20 35,45 20,30 5,10 10,10 30,20 35),(30 20,20 25,20 15,30 20)))"
		And the response has a "values.last_location_point" property
		And the response has a "values.links" property
		And the response has a "values.missing_status" property
		Then the guzzle status code should be 200

	@get
	Scenario: Finding a non-existent Post
		Given that I want to find a "Post"
		And that its "id" is "35"
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	@delete
	Scenario: Deleting a Post
		Given that I want to delete a "Post"
		And that its "id" is "1"
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		Then the guzzle status code should be 200

	@delete
	Scenario: Fail to delete a non existent Post
		Given that I want to delete a "Post"
		And that its "id" is "35"
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	@create
	Scenario: Creating a new Post with UTF-8 title
		Given that I want to make a new "Post"
		And that the request "data" is:
			"""
			{
				"form":1,
				"title":"SUMMARY REPORT (تقرير ملخص)",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"last_location":["atlanta"],
					"missing_status":["believed_missing"]
				},
				"tags":["explosion"]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the response has a "title" property
		And the "title" property equals "SUMMARY REPORT (تقرير ملخص)"
		And the "slug" property contains "summary-report-تقرير-ملخص"
		And the response has a "tags.0.id" property
		Then the guzzle status code should be 200


        @create @media
	Scenario: Creating a Post with media
		Given that I want to make a new "Post"
		And that the request "data" is:
			"""
			{
				"form":1,
				"title":"Author id 1",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"user":{
					"id": 1
				},
				"values":
				{
					"full_name":["David Kobia"],
					"description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
					"date_of_birth":[],
					"missing_date":["2012/09/25"],
					"missing_status":["believed_missing"],
					"last_location":["atlanta"],
					"media_test":[2]
				}
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the "values.media_test.0" property equals "2"
		Then the guzzle status code should be 200


	@resetFixture @search
	Scenario: Search All Posts by link attribute
		Given that I want to get all "Posts"
		And that the request "query string" is:
			"""
			values[links]=http://google.com
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "1"
		Then the guzzle status code should be 200

    @update
    Scenario: Users can assign roles to restrict publication of their posts
        Given that I want to update a "Post"
        And that its "id" is "105"
        And that the request "Authorization" header is "Bearer testbasicuser2"
        And that the request "data" is:
        """
        {
            "published_to":["admin"]
        }
        """
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the "published_to" property contains "admin"
        And the response has an "id" property
