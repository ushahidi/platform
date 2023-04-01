@setsFixture @oauth2Skip
Feature: Testing the Sets API
	Scenario: Creating a SavedSearch
		Given that I want to make a new "SavedSearch"
		And that the api_url is "api/v5"
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
		And the response has a "result.id" property
		And the type of the "result.id" property is "numeric"
		And the response has a "result.name" property
		And the "result.name" property equals "Search One"
		And the "result.featured" property equals "1"
		And the "result.view" property equals "map"
		And the "result.user_id" property equals "2"
		And the "result.filter.q" property equals "zombie"
		Then the guzzle status code should be 200

	Scenario: Creating a SavedSearch with search=0 is ignored
		Given that I want to make a new "SavedSearch"
		And that the api_url is "api/v5"
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
		And the response has a "result.id" property
		And the type of the "result.id" property is "numeric"
		And the response has a "result.name" property
		Then the guzzle status code should be 200

	Scenario: Updating a SavedSearch
		Given that I want to update a "SavedSearch"
		And that the api_url is "api/v5"
		And that the request "data" is:
			"""
			{
				"name":"Updated saved search Search One",
				"filter":{
					"q":"updated"
				}
			}
			"""
		And that its "id" is "4"
		When I request "/savedsearches"
		Then the response is JSON
		And the "result.id" property equals "4"
		And the response has a "result.name" property
		And the "result.name" property equals "Updated saved search Search One"
		And the "result.filter.q" property equals "updated"
		Then the guzzle status code should be 200

	Scenario: Updating a non-existent SavedSearch
		Given that I want to update a "SavedSearch"
		And that the api_url is "api/v5"
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
		And that the api_url is "api/v5"
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
		And that the api_url is "api/v5"
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
		And that the api_url is "api/v5"
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "meta.total" property
		And the type of the "meta.total" property is "numeric"
		And the "meta.total" property equals "3"
		Then the guzzle status code should be 200

	@resetFixture
	Scenario: Finding a non-existent SavedSearch
		Given that I want to find a "SavedSearch"
		And that the api_url is "api/v5"
		And that its "id" is "22"
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Finding a collection via SavedSearch fails
		Given that I want to find a "SavedSearch"
		And that the api_url is "api/v5"
		And that its "id" is "1"
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Finding a SavedSearch
		Given that I want to find a "SavedSearch"
		And that the api_url is "api/v5"
		And that its "id" is "4"
		When I request "/savedsearches"
		Then the response is JSON
		And the response has a "result.id" property
		And the type of the "result.id" property is "numeric"
		Then the guzzle status code should be 200

	Scenario: Deleting a SavedSearch
		Given that I want to delete a "SavedSearch"
		And that the api_url is "api/v5"
		And that its "id" is "4"
		When I request "/savedsearches"
		Then the guzzle status code should be 200

	Scenario: Deleting a non-existent SavedSearch
		Given that I want to delete a "SavedSearch"
		And that the api_url is "api/v5"
		And that its "id" is "22"
		When I request "/savedsearches"
		And the response has a "errors" property
		Then the guzzle status code should be 404

	Scenario: Deleting a collection via SavedSearch fails
		Given that I want to delete a "SavedSearch"
		And that the api_url is "api/v5"
		And that its "id" is "2"
		When I request "/savedsearches"
		And the response has a "errors" property
		Then the guzzle status code should be 404