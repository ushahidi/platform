# These are v3 tests ported over to v5

@post @rolesEnabled
Feature: Testing the Posts API

	@create
	Scenario: Creating a Post anonymously with no form
		Given that I want to make a new "Post"
		And that the oauth token is "testmanager"
		And that the api_url is "api/v5"
		And that the request "data" is:
			"""
			{
				"title": "A title",
				"description": ""
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "error" property
		And the type of the "error" property is "numeric"
		And the response has a "messages" property
		And the "messages" property equals "The V5 API requires a form_id for post creation."
		Then the guzzle status code should be 422

	@create
	Scenario: Creating a Post with a restricted Form with an Admin User
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testadminuser"
		And that the request "data" is:
			"""
			{
				"form_id":2,
				"title":"Test post",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"post_content": []
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "result.id" property
		And the type of the "result.id" property is "numeric"
		Then the guzzle status code should be 201

	@create
	Scenario: Creating a Post in a survey that requires "user" roles to create and does not require approval
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":2,
				"title":"Test post",
				"type":"report",
				"locale":"en_US",
				"post_content": [],
				"status": "published"
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "result.id" property
		And the type of the "result.id" property is "numeric"
		And the response has a "result.status" property
		And the "result.status" property equals "published"
		Then the guzzle status code should be 201

	@create
	Scenario: Creating a Post with a form that does not require approval but try to set status should pass
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":2,
				"title":"Test post",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"post_content": []
			}
			"""
		When I request "/posts"
		Then the response is JSON
		Then the guzzle status code should be 201

	@create
	Scenario: Creating an Post with invalid data returns an error
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
				{
				"title": "A title",
				"description": "",
				"locale": "en_US",
				"post_content": [
					{
						"id": 1,
						"type": "post",
						"fields": [
							{
								"id": 1,
								"type": "varchar",
								"translations": [],
								"value": {
									"value": "MY VARCHAR"
								}
							},
							{
								"id": 26,
								"type": "tags",
								"value": {
									"value": 1
								}

							}
						]
					},
					{
						"id": 2,
						"form_id": 1,
						"type": "task",
						"fields": [
							{
								"id": 13,
								"type": "varchar",
								"value": {
									"value": "is_note_author"
								}
							}
						]
					}
				],
				"completed_stages": [
					2,
					3
				],
				"published_to": [],
				"post_date": "2020-06-24T07:04:07.897Z",
				"enabled_languages": {},
				"content": "A description",
				"base_language": "",
				"type": "report",
				"form_id": 1
        	}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "error" property
		Then the guzzle status code should be 422

	@create
	Scenario: Creating a new Post with too many values for attribute returns an error
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":1,
				"title":"Test post",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"post_content":
				[
					{
						  "id": 1,
						  "form_id": 1,
						  "fields": [
							{
									  "id": 7,
									  "type": "varchar",
									  "value": {
										  "id": 23,
										  "post_id": 105,
										  "value": ["Atlantis", "ohno"],
										  "form_attribute_id": 7,
										  "created": null,
										  "translations": []
									  }
							  }
						  ]
					  }
				]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "error" property
		And the response has a "messages" property
		And the response has a "type" property
		And the "type" property equals "fields"
		Then the guzzle status code should be 422

	@create
	Scenario: Creating an Post without required fields returns an error
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":1,
				"title":"Invalid post",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"post_content":
				{
					"fields": [
						{
						  "id": 3,
						  "type": "varchar",
						  "value": {
							"value": "Full name",
							"translations": {
							  "es": {
								"value": "Full name ES"
							  }
							}
					  	}
				  	}
				  	]
				},
				"completed_stages":[1]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "error" property
		Then the guzzle status code should be 422

	@create
	Scenario: Creating an Post with existing (unmatched) user matches the same user_id
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":1,
				"title":"Invalid author",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"author_realname": "Robbie Mackay",
				"author_email": "robbie@ushahidi.com",
				"post_content":
				{

				}
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "result.user_id" property
		And the "result.user_id" property equals "1"
		Then the guzzle status code should be 201

	@create
	Scenario: Creating a Post with a restricted Form and incorrect role returns an error
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testimporter"
		And that the request "data" is:
			"""
			{
				"form_id":2,
				"title":"Test post",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"post_content":
				{

				},
				"tags":["explosion"],
				"completed_stages":[]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "errors.0.message" property
		Then the guzzle status code should be 403

	@create
	Scenario: Creating a Post with existing user by ID (authorized as admin user)
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":1,
				"title":"Author id 1",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"user":{
					"id": 1
				},
				"post_content": [
					{
						"id": 1,
						"type": "post",
						"fields": [
							{
								"id": 1,
								"type": "varchar",
								"translations": [],
								"value": {
									"value": "MY VARCHAR"
								}
							}
						]
					}
				]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "result.id" property
		And the "result.user_id" property equals "1"
		Then the guzzle status code should be 201

	@create
	Scenario: A normal user creates a Post with different user as author, API resolves it to the correct author id
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser2"
		And that the request "data" is:
			"""
			{
				"form_id":1,
				"title":"Author id 1",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"user":{
					"id": 1
				},
				"post_content":[
					{

						"fields": [

							  {
								  "id": 7,
								  "type": "varchar",
								  "value": {
									  "id": 23,
									  "post_id": 105,
									  "value": "Atlantis",
									  "form_attribute_id": 7,
									  "created": null,
									  "translations": []
								  }
							  }
						]
					}
				]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "result.user_id" property
		And the "result.user_id" property equals "3"
		Then the guzzle status code should be 201

	@create
	Scenario: Creating a Post with no user gets current uid
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":1,
				"title":"Invalid author",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"user":null,
				"post_content":[
					{

						"fields": [

							  {
								  "id": 7,
								  "type": "varchar",
								  "value": {
									  "id": 23,
									  "post_id": 105,
									  "value": "Atlantis",
									  "form_attribute_id": 7,
									  "created": null,
									  "translations": []
								  }
							  }
						]
					}
				]
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "result.id" property
		And the "result.user_id" property equals "1"
		Then the guzzle status code should be 201

	@update
	Scenario: Updating a non-existent Post
		Given that I want to update a "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":1,
				"title":"Updated Test Post",
				"type":"report",
				"status":"published",
				"locale":"en_US"
			}
			"""
		And that its "id" is "40"
		When I request "/posts"
		Then the response is JSON
		And the response has a "error" property
		Then the guzzle status code should be 404

	@resetFixture @update
	Scenario: Updating user info on a Post (as admin)
		Given that I want to update a "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":1,
				"title":"Updated Test Post",
				"type":"report",
				"status":"published",
				"locale":"en_US",
				"user":{
					"id": 4
				}
			}
			"""
		And that its "id" is "1"
		When I request "/posts"
		Then the response is JSON
		Then the guzzle status code should be 403

	@update @resetFixture
	Scenario: Updating author info on a Post (as admin)
		Given that I want to update a "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":1,
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
		Then the guzzle status code should be 403


	@resetFixture @update
	Scenario: A normal user updating a post with a new user id should get the user id ignored
		Given that I want to update a "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":1,
				"title":"Updated Test Post",
				"type":"report",
				"status":"published",
				"locale":"en_US",
				"user_id": 4,
				"post_content": {}
			}
			"""
		And that its "id" is "110"
		When I request "/posts"
		Then the response is JSON
		And the response has a "result.user_id" property
		And the "result.user_id" property equals "1"
		Then the guzzle status code should be 200

	@resetFixture @create
	Scenario: Creating a Post with non-existent Form
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":35,
				"title":"Updated Test Post",
				"type":"report",
				"status":"published",
				"locale":"en_US",
				"post_content":
				{
				}
			}
			"""
		And that its "id" is "1"
		When I request "/posts"
		Then the response is JSON
		And the response has a "error" property
		And the response has a "messages" property
		And the "messages.form_id.0" property equals "fields.form_id must exist"
		Then the guzzle status code should be 422

	@delete
	Scenario: Deleting a Post
		Given that I want to delete a "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that its "id" is "110"
		When I request "/posts"
		Then the response is JSON
		And the response has a "result.deleted" property
		Then the guzzle status code should be 200

	Scenario: Checking only that post got deleted
		Given that I want to find a "Post"
		And that the api_url is "api/v5"
		And that its "id" is "110"
		When I request "/posts"
		Then the response is JSON
		Then the guzzle status code should be 404

	@delete
	Scenario: Failing to delete a Post (lack of ownership for regular user)
		Given that I want to delete a "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that its "id" is "1"
		When I request "/posts"
		Then the guzzle status code should be 403

	@delete
	Scenario: Fail to delete a non existent Post
		Given that I want to delete a "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that its "id" is "35"
		When I request "/posts"
		Then the guzzle status code should be 403

	@create
	Scenario: Creating a new Post with UTF-8 title
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
			{
				"form_id":1,
				"title":"SUMMARY REPORT (تقرير ملخص)",
				"type":"report",
				"status":"draft",
				"locale":"en_US",
				"post_content":{}
			}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "result.id" property
		And the type of the "result.id" property is "numeric"
		And the response has a "result.title" property
		And the "result.title" property equals "SUMMARY REPORT (تقرير ملخص)"
		#And the "result.slug" property contains "summary-report-تقرير-ملخص"
		Then the guzzle status code should be 201

  @update
  Scenario: Users can assign roles to restrict publication of their posts
      Given that I want to update a "Post"
	  And that the api_url is "api/v5"
	  And that the oauth token is "testbasicuser2"
      And that its "id" is "105"
      And that the request "data" is:
      """
      {
              "id": 105,
              "form_id": 1,
              "user_id": 3,
              "type": "report",
              "title": "Original post",
              "slug": null,
              "content": "Some description",
              "author_email": null,
              "author_realname": null,
              "status": "published",
              "published_to": ["admin"],
              "locale": "en_us",
              "created": "2013-07-05 00:00:00",
              "updated": null,
              "post_date": "2013-07-04 23:36:05",
              "base_language": "",
              "categories": [],
              "completed_stages": [],
              "post_content": {}
      }
      """
      When I request "/posts"
      Then the guzzle status code should be 200
      And the response is JSON
      And the "result.published_to" property contains "admin"
      And the response has a "result.id" property

	@create
	Scenario: Creating a new Post with invalid location latitude
		Given that I want to make a new "Post"
		And that the api_url is "api/v5"
		And that the oauth token is "testbasicuser"
		And that the request "data" is:
			"""
				{
				"title": "A title",
				"description": "",
				"locale": "en_US",
				"post_content": [
					{
						"id": 1,
						"type": "post",
						"fields": [
							{
								"id": 1,
								"type": "varchar",
								"translations": [],
								"value": {
									"value": "MY VARCHAR"
								}
							},
							{
								"id": 2,
								"type": "point",
								"value": {
									"value": {
										"lat": 8.892817463050697,
										"lon": 222.840418464728486
									}
								}
							},
							{
								"id": 3,
								"type": "varchar",
								"translations": [],
								"value": {
									"value": "A full name"
								}
							},
							{
								"id": 5,
								"type": "datetime",
								"value": "2020-06-01T07:04:10.921Z"
							},
							{
								"id": 6,
								"type": "datetime",
								"value": "2020-06-02T07:04:10.921Z"
							},
							{
								"id": 7,
								"type": "varchar",
								"value": {
									"value": "Uruguay"
								}
							},
							{
								"id": 8,
								"type": "point",
								"value": {
									"value": {
										"lat": -22.03321543231222,
										"lon": 27.935730246117373
									}
								}
							},
							{
								"id": 10,
								"type": "varchar",
								"value": {
									"value": "information_sought"
								}
							},
							{
								"id": 11,
								"type": "varchar",
								"value": {
									"value": "https://google.com"
								}
							},
							{
								"id": 12,
								"type": "point",
								"value": {
									"value": {
										"lat": -57.544489720135516,
										"lon": 169.81215934564818
									}
								}
							},
							{
								"id": 14,
								"type": "media",
								"value": {
									"value": null
								}
							},
							{
								"id": 15,
								"type": "varchar",
								"value": {
									"value": [
										"medical_evacuation"
									]
								}
							},
							{
								"id": 25,
								"type": "markdown",
								"value": {
									"value": "#markdowny"
								}
							},
							{
								"id": 26,
								"type": "tags",
								"value": {
									"value": [1,2]
								}

							}
						]
					},
					{
						"id": 2,
						"form_id": 1,
						"type": "task",
						"fields": [
							{
								"id": 13,
								"type": "varchar",
								"value": {
									"value": "is_note_author"
								}
							}
						]
					}
				],
				"completed_stages": [
					2,
					3
				],
				"published_to": [],
				"post_date": "2020-06-24T07:04:07.897Z",
				"enabled_languages": {},
				"content": "A description",
				"base_language": "",
				"type": "report",
				"form_id": 1
				}
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "error" property
		And the response has a "messages" property
		Then the guzzle status code should be 422
