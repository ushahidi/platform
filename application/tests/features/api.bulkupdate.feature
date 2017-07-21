@post @oauth2Skip
Feature: Testing the Posts Bulk Update API

	@create
	Scenario: Updating status of Posts with anonymous user
		Given that I want to bulk update "Post"
		And that the request "Authorization" header is "Bearer testanon"
		And that the request "data" is:
			"""
			{
				"actions": {
					"status": "draft"
				},
				"filters": {
					"tags": {
						"any": [3,4]
					}
				}

			}
			"""
		When I request "/posts/bulk/update"
		Then the guzzle status code should be 403

	@create
	Scenario: Updating status of Posts with an invalid status
		Given that I want to bulk update "Post"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "data" is:
			"""
			{
				"actions": {
					"status": "dkflsk"
				},
				"filters": {
					"tags": {
						"all": [3,4]
					}
				}

			}
			"""
		When I request "/posts/bulk/update"
		Then the guzzle status code should be 422

	@create
	Scenario: Updating status of Posts with an invalid filter
		Given that I want to bulk update "Post"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "data" is:
			"""
			{
				"actions": {
					"status": "draft"
				},
				"filters": {
					"maps_id": 4
				}

			}
			"""
		When I request "/posts/bulk/update"
		Then the guzzle status code should be 422

	Scenario: Update Posts with an empty `actions` and `filter` values
	Given that I want to bulk update "Post"
	And that the request "Authorization" header is "Bearer testadminuser"
		And that the request "data" is:
			"""
			{
				"actions": {

				},
				"filters": {

				}

			}
			"""
		When I request "/posts/bulk/update"
		And the response has a "errors.1.title" property
		And the "errors.1.title" property equals "Invalid bulk actions"
		And the response has a "errors.2.title" property
		And the "errors.2.title" property equals "Must have valid filters"
		Then the guzzle status code should be 422


	@create
	Scenario: Updating status of Posts with a malformed payload
		Given that I want to bulk update "Post"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "data" is:
			"""
			{
				"actions": {
					"status": "dkflsk"
				},
				"filter": {
					"tags": {
						"all": [3,4]
					}

			}
			"""
		When I request "/posts/bulk/update"
		Then the guzzle status code should be 400

	@create
	Scenario: Updating status of Posts to `draft` with a manager user
		Given that I want to bulk update "Post"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "data" is:
			"""
			{
				"actions": {
					"status": "draft"
				},
				"filters": {
					"tags": {
						"all": [3,4]
					}
				}

			}
			"""
		When I request "/posts/bulk/update"
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "1"
		And the response has a "actions.status" property
		And the "actions.status" property equals "draft"
		Then the guzzle status code should be 200

	@search
	Scenario: Check status of Posts after update status to `draft` with `testmanager`
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
		And the "count" property equals "1"
		And the "results.0.status" property equals "draft"
		And the response has a "results.0.id" property
		And the "results.0.id" property equals "1"
		Then the guzzle status code should be 200

	@create
	Scenario: Updating status of Posts to `published` with a manager user
		Given that I want to bulk update "Post"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "data" is:
			"""
			{
				"actions": {
					"status": "published"
				},
				"filters": {
					"tags": {
						"all": [3,4]
					}
				}

			}
			"""
		When I request "/posts/bulk/update"
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "1"
		And the response has a "actions.status" property
		And the "actions.status" property equals "published"
		Then the guzzle status code should be 200

	@search
	Scenario: Check status of Posts after update status to `published`
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
		And the "count" property equals "1"
		And the "results.0.status" property equals "published"
		And the response has a "results.0.id" property
		And the "results.0.id" property equals "1"
		Then the guzzle status code should be 200

	@resetFixture @create
	Scenario: Updating status of Posts with an admin user
	Given that I want to bulk update "Post"
	And that the request "Authorization" header is "Bearer testadminuser"
		And that the request "data" is:
			"""
			{
				"actions": {
					"status": "draft"
				},
				"filters": {
					"tags": {
						"any": [3,4]
					}
				}

			}
			"""
		When I request "/posts/bulk/update"
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "2"
		And the response has a "actions.status" property
		And the "actions.status" property equals "draft"
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
		And the "count" property equals "2"
		And the "results.0.status" property equals "draft"
		And the response has a "results.0.id" property
		And the "results.0.id" property equals "99"
		And the "results.1.status" property equals "draft"
		And the response has a "results.1.id" property
		And the "results.1.id" property equals "1"
		Then the guzzle status code should be 200

	@resetFixture @create
	Scenario: Updating status of Posts filtering by status
	Given that I want to bulk update "Post"
	And that the request "Authorization" header is "Bearer testadminuser"
		And that the request "data" is:
			"""
			{
				"actions": {
					"status": "published"
				},
				"filters": {
					"status": "archived"
				}

			}
			"""
		When I request "/posts/bulk/update"
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "6"
		And the response has a "actions.status" property
		And the "actions.status" property equals "published"
		Then the guzzle status code should be 200

	@search
	Scenario: Check status of Posts after update from `archived` to `published`
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "query string" is:
			"""
			status=published
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "18"
		And the "results.0.status" property equals "published"
		And the response has a "results.0.id" property
		And the "results.0.id" property equals "120"
		And the "results.1.status" property equals "published"
		And the response has a "results.1.id" property
		And the "results.1.id" property equals "121"
		Then the guzzle status code should be 200

	@resetFixture @postLimitsEnabled @create
	Scenario: Updating status of Posts filtering by status with post limit
	Given that I want to bulk update "Post"
	And that the request "Authorization" header is "Bearer testadminuser"
		And that the request "data" is:
			"""
			{
				"actions": {
					"status": "published"
				},
				"filters": {
					"status": "archived"
				}

			}
			"""
		When I request "/posts/bulk/update"
		And the response has a "errors.1.title" property
		And the "errors.1.title" property equals "limit::posts"
		Then the guzzle status code should be 422

	@search
	Scenario: Check status of Posts after update from `archived` to `published`
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testmanager"
		And that the request "query string" is:
			"""
			status=published
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "12"
		And the "results.0.status" property equals "published"
		And the response has a "results.0.id" property
		And the "results.0.id" property equals "120"
		Then the guzzle status code should be 200

	@resetFixture @create
	Scenario: Updating status of Posts with incomplete required stages
	Given that I want to bulk update "Post"
	And that the request "Authorization" header is "Bearer testadminuser"
		And that the request "data" is:
			"""
			{
				"actions": {
					"status": "published"
				},
				"filters": {
					"set": "7"
				}

			}
			"""
		When I request "/posts/bulk/update"
		And the response has a "errors.1.title" property
		Then the guzzle status code should be 422