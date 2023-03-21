 @oauth2Skip
Feature: Testing the Users API

	Scenario: Creating a User
		Given that I want to make a new "user"
		And that the api_url is "api/v5"
		And that the request "data" is:
			"""
			{
				"email":"linda@ushahidi.com",
				"realname":"Linda Kamau",
				"password":"testing",
				"role":"admin"
			}
			"""
		When I request "/users"
		Then the response is JSON
		And the response has a "data.id" property
		And the type of the "data.id" property is "numeric"
		And the response has a "data.email" property
		And the "data.email" property equals "linda@ushahidi.com"
		And the "data.role" property equals "admin"
		And the response does not have a "data.password" property
		Then the guzzle status code should be 200
 	@usersFixture
	Scenario: Updating a User
		Given that I want to update a "user"
		And that the api_url is "api/v5"
		And that the request "data" is:
			"""
			{
				"email":"Mike@ushahidi.com",
				"realname":"Mike Mackay",
				"password":"testing",
				"role":"admin",
				"contacts": [
					{
						"id": "1",
						"user_id": "1",
						"data_provider": null,
						"type": "phone",
						"contact": "123456789",
						"created": "0",
						"updated": null,
						"can_notify": "0",
						"name": null
					},
					{
						"id": "4",
						"user_id": "1",
						"data_provider": null,
						"type": "email",
						"contact": "robbie@ushahidi.com",
						"created": "0",
						"updated": null,
						"can_notify": "0",
						"name": null
					}
				]
			}
			"""
		And that its "id" is "1"
		When I request "/users"
		Then the response is JSON
		And the response has a "data.id" property
		And the type of the "data.id" property is "numeric"
		And the "data.id" property equals "1"
		And the response has a "data.email" property
		And the "data.email" property equals "Mike@ushahidi.com"
		And the "data.role" property equals "admin"
		Then the guzzle status code should be 200


	@resetFixture
	Scenario: A normal user should not be able to change their own role
		Given that I want to update a "user"
		And that the oauth token is "testbasicuser"
		And that the api_url is "api/v5"
		And that the request "data" is:
			"""
			{
				"role":"admin"
			}
			"""
		And that its "id" is "1"
		When I request "/users"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 403

	Scenario: Updating a non-existent User
		Given that I want to update a "user"
		And that the api_url is "api/v5"
		And that the request "data" is:
			"""
			{
				"email":"tom@ushahidi.com",
				"realname":"Tom Kamau",
				"password":"tomkamau"
			}
			"""
		And that its "id" is "15"
		When I request "/users"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	@resetFixture
	Scenario: Listing All Users
		Given that I want to get all "Users"
		When I request "/users"
		And that the api_url is "api/v5"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "10"
		Then the guzzle status code should be 200

	@resetFixture
	Scenario: Search All Users
		Given that I want to get all "Users"
		And that the api_url is "api/v5"
		And that the request "query string" is:
			"""
			q=rob
			"""
		When I request "/users"
		Then the response is JSON
		And the "meta.total" property equals "1"
		And the "data.0.realname" property equals "Robbie Mackay"
		Then the guzzle status code should be 200

	Scenario: Finding a User
		Given that I want to find a "User"
		And that the api_url is "api/v5"
		And that its "id" is "1"
		When I request "/users"
		Then the response is JSON
		And the response has a "data.id" property
		And the type of the "data.id" property is "numeric"
		And the "data.realname" property equals "Robbie Mackay"
		Then the guzzle status code should be 200

	Scenario: Finding a User as Admin gives full details
		Given that I want to find a "User"
		And that the api_url is "api/v5"
		And that its "id" is "3"
		And that the oauth token is "defaulttoken"
		When I request "/users"
		Then the response is JSON
		And the response has a "data.id" property
		And the type of the "data.id" property is "numeric"
		And the "data.realname" property equals "Test User"
		And the "data.email" property equals "test@v3.ushahidi.com"
		Then the guzzle status code should be 200

	Scenario: Loading own user gives full details
		Given that I want to find a "User"
		And that the api_url is "api/v5"
		And that its "id" is "me"
		And that the oauth token is "testbasicuser"
		When I request "/users"
		Then the response is JSON
		And the response has a "data.id" property
		And the type of the "data.id" property is "numeric"
		And the "data.realname" property equals "Robbie Mackay"
		And the "data.email" property equals "robbie@ushahidi.com"
		Then the guzzle status code should be 200

	Scenario: Loading own user without login
		Given that I want to find a "User"
		And that the api_url is "api/v5"
		And that its "id" is "me"
		And that the oauth token is "testanon"
		When I request "/users"
		Then the response is JSON
		Then the guzzle status code should be 404

	Scenario: Finding a User as anonymous user does not give details
		Given that I want to find a "User"
		And that the api_url is "api/v5"
		And that its "id" is "1"
		And that the oauth token is "testanon"
		When I request "/users"
		Then the guzzle status code should be 403
		Then the response is JSON

	Scenario: Finding a non-existent user
		Given that I want to find a "User"
		And that the api_url is "api/v5"
		And that its "id" is "18"
		When I request "/users"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Deleting a User
		Given that I want to delete a "User"
		And that the api_url is "api/v5"
		And that its "id" is "1"
		When I request "/users"
		Then the guzzle status code should be 200

	Scenario: Deleting a non-existent User
		Given that I want to delete a "User"
		And that the api_url is "api/v5"
		And that its "id" is "18"
		When I request "/users"
		And the response has a "errors" property
		Then the guzzle status code should be 404
