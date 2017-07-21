@post @oauth2Skip
Feature: Testing the Posts Bulk Delete API

	@create
	Scenario: Deleting Posts with anonymous user
		Given that I want to bulk delete "Post"
		And that the request "Authorization" header is "Bearer testanon"
		And that the request "data" is:
			"""
			{
				"filters": {
					"tags": {
						"any": [3,4]
					}
				}

			}
			"""
		When I request "/posts/bulk/delete"
		Then the guzzle status code should be 403

	@create
	Scenario: Deleting Posts with an invalid filter
		Given that I want to bulk delete "Post"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "data" is:
			"""
			{
				"filters": {
					"maps_id": 4
				}

			}
			"""
		When I request "/posts/bulk/delete"
		And the response has a "errors.1.title" property
		And the "errors.1.title" property equals "Must have valid filters"
		Then the guzzle status code should be 422

	Scenario: Delete Posts with an empty filter
	Given that I want to bulk delete "Post"
	And that the request "Authorization" header is "Bearer testadminuser"
		And that the request "data" is:
			"""
			{
				"filters": {

				}

			}
			"""
		When I request "/posts/bulk/delete"
		And the response has a "errors.1.title" property
		And the "errors.1.title" property equals "Must have valid filters"
		Then the guzzle status code should be 422

	@create
	Scenario: Deleting Posts with an invalid filter
		Given that I want to bulk delete "Post"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "data" is:
			"""
			{
				"filters": {
					"maps_id": 4
				}

			}
			"""
		When I request "/posts/bulk/delete"
		Then the guzzle status code should be 422

	@create
	Scenario: Deleting Posts with no payload
		Given that I want to bulk delete "Post"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "data" is:
			"""
			"""
		When I request "/posts/bulk/delete"
		Then the guzzle status code should be 400

	@create
	Scenario: Deleting Posts with a manager user
		Given that I want to bulk delete "Post"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "data" is:
			"""
			{
				"filters": {
					"tags": {
						"all": [3,4]
					}
				}

			}
			"""
		When I request "/posts/bulk/delete"
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "1"
		And the response has a "actions.deleted" property
		And the "actions.deleted" property equals "true"
		Then the guzzle status code should be 200

	@search
	Scenario: Check status of Posts after bulk deletion with `testmanager`
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "query string" is:
			"""
			tags[all]=3,4&status=draft,published,archived
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "0"
		Then the guzzle status code should be 200

	@resetFixture @create
	Scenario: Delete Posts with an admin user
	Given that I want to bulk delete "Post"
	And that the request "Authorization" header is "Bearer testadminuser"
		And that the request "data" is:
			"""
			{
				"filters": {
					"tags": {
						"any": [3,4]
					}
				}

			}
			"""
		When I request "/posts/bulk/delete"
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "2"
		And the response has a "actions.deleted" property
		And the "actions.deleted" property equals "true"
		Then the guzzle status code should be 200

	@search
	Scenario: Check status of Posts after update
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "query string" is:
			"""
			tags[any]=3,4&status=draft,published,archived
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "0"
		Then the guzzle status code should be 200

	@resetFixture @create
	Scenario: Delete `archived` Posts filtered by status
	Given that I want to bulk delete "Post"
	And that the request "Authorization" header is "Bearer testadminuser"
		And that the request "data" is:
			"""
			{
				"filters": {
					"status": "archived"
				}

			}
			"""
		When I request "/posts/bulk/delete"
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "6"
		And the response has a "actions.deleted" property
		And the "actions.deleted" property equals "true"
		Then the guzzle status code should be 200

	@search
	Scenario: Get `archived` Posts after deleting `archived` posts
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "query string" is:
			"""
			status=archived
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "0"
		Then the guzzle status code should be 200

	@search
	Scenario: Get all Posts after deleting `archived` posts
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "query string" is:
			"""
			status=archived,published,draft
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "18"
		Then the guzzle status code should be 200