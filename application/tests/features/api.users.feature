@usersFixture @oauth2Skip
Feature: Testing the Users API

	Scenario: Creating a User
		Given that I want to make a new "user"
		And that the request "data" is:
			"""
			{
				"email":"linda@ushahidi.com",
				"realname":"Linda Kamau",
				"username":"kamaulynder",
				"password":"testing",
				"role":"admin"
			}
			"""
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the response has a "email" property
		And the "email" property equals "linda@ushahidi.com"
		And the "role" property equals "admin"
		And the response does not have a "password" property
		Then the guzzle status code should be 200

	Scenario: Updating a User
		Given that I want to update a "user"
		And that the request "data" is:
			"""
			{
				"email":"robbie@ushahidi.com",
				"realname":"Robbie Mackay",
				"username":"rjmackay",
				"password":"testing",
				"role":"admin"
			}
			"""
		And that its "id" is "1"
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "id" property equals "1"
		And the response has a "email" property
		And the "email" property equals "robbie@ushahidi.com"
		And the "role" property equals "admin"
		Then the guzzle status code should be 200

	@resetFixture
	Scenario: A normal user should not be able to change their own role
		Given that I want to update a "user"
		And that the request "Authorization" header is "Bearer testbasicuser"
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
		And that the request "data" is:
			"""
			{
				"email":"tom@ushahidi.com",
				"realname":"Tom Kamau",
				"username":"tom",
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
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "5"
		Then the guzzle status code should be 200

	@resetFixture
	Scenario: Search All Users
		Given that I want to get all "Users"
		And that the request "query string" is:
			"""
			q=rob
			"""
		When I request "/users"
		Then the response is JSON
		And the "count" property equals "1"
		And the "results.0.username" property equals "robbie"
		Then the guzzle status code should be 200

	Scenario: Finding a User
		Given that I want to find a "User"
		And that its "id" is "1"
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "username" property equals "robbie"
		Then the guzzle status code should be 200

	Scenario: Finding a User as admin gives full details
		Given that I want to find a "User"
		And that its "id" is "3"
		And that the request "Authorization" header is "Bearer defaulttoken"
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "username" property equals "test"
		And the "email" property equals "test@v3.ushahidi.com"
		Then the guzzle status code should be 200

	Scenario: Loading own user gives full details
		Given that I want to find a "User"
		And that its "id" is "me"
		And that the request "Authorization" header is "Bearer testbasicuser"
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "username" property equals "robbie"
		And the "email" property equals "robbie@ushahidi.com"
		Then the guzzle status code should be 200

	Scenario: Finding a User as anonymous user gives partial details
		Given that I want to find a "User"
		And that its "id" is "1"
		And that the request "Authorization" header is "Bearer testanon"
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the response has a "realname" property
		And the response has a "username" property
		And the response does not have a "email" property
		And the response does not have a "logins" property
		And the response does not have a "failed_attempts" property
		And the response does not have a "last_login" property
		And the response does not have a "last_attempt" property
		Then the guzzle status code should be 200

	Scenario: Finding a non-existent user
		Given that I want to find a "User"
		And that its "id" is "18"
		When I request "/users"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Deleting a User
		Given that I want to delete a "User"
		And that its "id" is "1"
		When I request "/users"
		Then the guzzle status code should be 200

	Scenario: Deleting a non-existent User
		Given that I want to delete a "User"
		And that its "id" is "18"
		When I request "/users"
		And the response has a "errors" property
		Then the guzzle status code should be 404

