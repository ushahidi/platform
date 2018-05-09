Feature: Testing the current user API
	@oauth2Skip
	Scenario: Get the current User
		Given that I want to find a "User"
		And that its "id" is "me"
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "email" property equals "admin@ushahidi.com"
		Then the guzzle status code should be 200

	@oauth2Skip
	Scenario: Updating the current user
		Given that I want to update a "user"
		And that the request "data" is:
			"""
			{
				"email":"admin@v3.ushahidi.com",
				"realname":"Admin User"
			}
			"""
		And that its "id" is "me"
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "id" property equals "2"
		And the response has a "email" property
		And the "email" property equals "admin@v3.ushahidi.com"
		Then the guzzle status code should be 200

	Scenario: Get the current user fails if no user authenticated
		Given that I want to find a "User"
		And that the oauth token is "testanon"
		And that its "id" is "me"
		When I request "/users"
		Then the response is JSON
		Then the guzzle status code should be 404

	Scenario: Non admin user can update their own profile
		Given that I want to update a "user"
		And that the oauth token is "testbasicuser2"
		And that the request "data" is:
			"""
			{
				"email":"test2@v3.ushahidi.com",
				"realname":"Test User, Jr"
			}
			"""
		And that its "id" is "me"
		When I request "/users"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "id" property equals "3"
		And the response has a "email" property
		And the "email" property equals "test2@v3.ushahidi.com"
		Then the guzzle status code should be 200

