@usersFixture
Feature: Testing the Users API

	Scenario: Creating a User
		Given that I want to add a new "user"
		And that the request "data" is:
			"""
			{
				"email":"linda@ushahidi.com",
				"first_name":"Linda",
				"last_name":"Kamau",
				"username":"kamaulynder",
				"password":"b3154acf3a344170077d11bdb5fff31532f679a1919e716a02"

			}
			"""
		When I request "/users""
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the response has a "email" property
		And the "email" property equals "linda@ushahidi.com"
		Then the response status code should be 200

	Scenario: Updating a User
		Given that I want to update a "user"
		And that the request "data" is:
			"""
			{
				"email":"linda@ushahidi.com",
				"first_name":"Lindah",
				"last_name":"Kamau",
				"username":"kamaulynder",
				"password":"b3154acf3a344170077d11bdb5fff31532f679a1919e716a02"
			}
			"""
		And that its "id" is "1"
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "id" property equals "1"
		And the response has a "email" property
		And the "email" property equals "linda@ushahidi.com"
		Then the response status code should be 200

	Scenario: Updating a non-existent User
		Given that I want to update a "user"
		And that the request "data" is:
			"""
			{
				"email":"tom@ushahidi.com",
				"first_name":"Tom",
				"last_name":"Kamau",
				"username":"tom",
				"password":"tomkamau"
			}
			"""
		And that its "id" is "15"
		When I request "/users"
		Then the response is JSON
		And the response has a "errors" property
		Then the response status code should be 404

	Scenario: Listing All Users
		Given that I want to get all "Users"
		When I request "/users"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "2"
		Then the response status code should be 200

	Scenario: Search All Users
		Given that I want to get all "Users"
		And that the request "query string" is:
			"""
			q=Kamau
			"""
		When I request "/users"
		Then the response is JSON
		And the "count" property equals "1"
		And the "results.0.last_name" property equals "Kamau"
		Then the response status code should be 200

	Scenario: Finding a User
		Given that I want to find a "User"
		And that its "id" is "1"
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		Then the response status code should be 200

	Scenario: Finding a non-existent user
		Given that I want to find a "User"
		And that its "id" is "18"
		When I request "/users"
		Then the response is JSON
		And the response has a "errors" property
		Then the response status code should be 404

	Scenario: Deleting a User
		Given that I want to delete a "User"
		And that its "id" is "1"
		When I request "/users"
		Then the response status code should be 200

	Scenario: Deleting a non-existent User
		Given that I want to delete a "User"
		And that its "id" is "18"
		When I request "/users"
		And the response has a "errors" property
		Then the response status code should be 404

