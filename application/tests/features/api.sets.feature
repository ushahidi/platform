@setsFixture @oauth2Skip
Feature: Testing the Sets API

	Scenario: Creating a Set
		Given that I want to make a new "set"
		And that the request "data" is:
			"""
			{
				"name":"Set One",
				"filter":"Set filter",
				"featured": 1,
				"search": 1,
				"filter": {
					"q":"zombie"
				},
				"view":"map",
				"view_options":[],
				"visible_to":[]
			}
			"""
		When I request "/sets"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the response has a "name" property
		And the "name" property equals "Set One"
		And the "featured" property equals "1"
		And the "search" property equals "1"
		And the "filter.q" property equals "zombie"
		And the "view" property equals "map"
		Then the guzzle status code should be 200

	Scenario: Updating a Set
		Given that I want to update a "set"
		And that the request "data" is:
			"""
			{
				"name":"Updated Set One",
				"filter":"updated set filter"
			}
			"""
		And that its "id" is "1"
		When I request "/sets"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the "id" property equals "1"
		And the response has a "name" property
		And the "name" property equals "Updated Set One"
		Then the guzzle status code should be 200

	Scenario: Updating a non-existent Set
		Given that I want to update a "set"
		And that the request "data" is:
			"""
			{
				"name":"Updated Set",
				"filter":"updated filter"
			}
			"""
		And that its "id" is "20"
		When I request "/sets"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Non admin user trying to make a set featured fails
		Given that I want to update a "set"
		And that the request "Authorization" header is "Bearer testbasicuser2"
		And that the request "data" is:
			"""
			{
				"name":"Updated Set One",
				"filter":"updated set filter",
				"featured":1
			}
			"""
		And that its "id" is "2"
		When I request "/sets"
		Then the response is JSON
		Then the guzzle status code should be 403

	@resetFixture
	Scenario: Listing All Sets
		Given that I want to get all "Sets"
		When I request "/sets"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "3"
		Then the guzzle status code should be 200

	@resetFixture
	Scenario: Listing All Sets as a normal user doesn't return admin set
		Given that I want to get all "Sets"
		And that the request "Authorization" header is "Bearer testbasicuser2"
		When I request "/sets"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "2"
		Then the guzzle status code should be 200

	@resetFixture
	Scenario: Search All Sets
		Given that I want to get all "Sets"
		And that the request "query string" is:
			"""
			q=Explo
			"""
		When I request "/sets"
		Then the response is JSON
		And the "count" property equals "1"
		And the "results.0.name" property equals "Explosion"
		Then the guzzle status code should be 200

	Scenario: Finding a Set
		Given that I want to find a "Set"
		And that its "id" is "1"
		When I request "/sets"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		Then the guzzle status code should be 200

	Scenario: Finding a non-existent Set
		Given that I want to find a "Set"
		And that its "id" is "22"
		When I request "/sets"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Deleting a Set
		Given that I want to delete a "Set"
		And that its "id" is "1"
		When I request "/sets"
		Then the guzzle status code should be 200

	Scenario: Deleting a non-existent Set
		Given that I want to delete a "Set"
		And that its "id" is "22"
		When I request "/sets"
		And the response has a "errors" property
		Then the guzzle status code should be 404

