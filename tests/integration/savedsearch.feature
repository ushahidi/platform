@setsFixture @oauth2Skip
Feature: Testing the Sets API

	Scenario: Creating a SavedSearch
		Given that I want to make a new "SavedSearch"
		And that the request "data" is:
			"""
			{
				"name":"Search One",
				"filter": {
					"q":"zombie"
				},
				"featured": 1,
				"view":"map",
				"view_options":[],
				"role":[]
			}
			"""
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the response has a "name" property
		And the "name" property equals "Search One"
		And the "featured" property equals "1"
		And the "view" property equals "map"
		And the "filter.q" property equals "zombie"
		Then the guzzle status code should be 200

	Scenario: Creating a SavedSearch with search=0 is ignored
		Given that I want to make a new "collection"
		And that the request "data" is:
			"""
			{
				"name":"Set One",
				"featured": 1,
				"search":"0",
				"filter":{
					"q":"zombie"
				},
				"view":"map",
				"view_options":[],
				"role":[]
			}
			"""
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		And the response has a "name" property
		And the response does not have a "search" property
		Then the guzzle status code should be 200

	Scenario: Updating a SavedSearch
		Given that I want to update a "SavedSearch"
		And that the request "data" is:
			"""
			{
				"name":"Updated Search One",
				"filter":{
					"q":"updated"
				}
			}
			"""
		And that its "id" is "4"
		When I request "/savedsearches"
		Then the response is JSON
		And the "id" property equals "4"
		And the response has a "name" property
		And the "name" property equals "Updated Search One"
		And the "filter.q" property equals "updated"
		Then the guzzle status code should be 200

	Scenario: Updating a non-existent SavedSearch
		Given that I want to update a "SavedSearch"
		And that the request "data" is:
			"""
			{
				"name":"Updated Set",
				"filter":"updated filter"
			}
			"""
		And that its "id" is "20"
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Updating a Collection via SavedSearch API fails
		Given that I want to update a "collection"
		And that the request "data" is:
			"""
			{
				"name":"Updated Set",
				"filter":"updated filter"
			}
			"""
		And that its "id" is "2"
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Non admin user trying to make a SavedSearch featured fails
		Given that I want to update a "SavedSearch"
		And that the oauth token is "testbasicuser2"
		And that the request "data" is:
			"""
			{
				"name":"Updated Search One",
				"filter":"updated search filter",
				"featured":1
			}
			"""
		And that its "id" is "5"
		When I request "/savedsearches"
		Then the response is JSON
		Then the guzzle status code should be 403

	@resetFixture
	Scenario: Listing All SavedSearches
		Given that I want to get all "SavedSearch"
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "3"
		Then the guzzle status code should be 200

	@resetFixture
	Scenario: Finding a non-existent SavedSearch
		Given that I want to find a "SavedSearch"
		And that its "id" is "22"
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Finding a collection via SavedSearch fails
		Given that I want to find a "SavedSearch"
		And that its "id" is "1"
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Finding a SavedSearch
		Given that I want to find a "SavedSearch"
		And that its "id" is "4"
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
		Then the guzzle status code should be 200

	Scenario: Deleting a SavedSearch
		Given that I want to delete a "SavedSearch"
		And that its "id" is "4"
		When I request "/savedsearches"
		Then the guzzle status code should be 200

	Scenario: Deleting a non-existent SavedSearch
		Given that I want to delete a "SavedSearch"
		And that its "id" is "22"
		When I request "/savedsearches"
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Deleting a collection via SavedSearch fails
		Given that I want to delete a "SavedSearch"
		And that its "id" is "2"
		When I request "/savedsearches"
		And the response has a "errors" property
		Then the guzzle status code should be 404
