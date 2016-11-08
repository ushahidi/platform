@post @oauth2Skip
Feature: Testing the Posts Bulk Action API

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
	Scenario: Updating status of Posts with a manager user
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
		Then the guzzle status code should be 400

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
		Then the guzzle status code should be 400

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

	@search
	Scenario: Check status of Posts after update
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testadminuser"
		And that the request "query string" is:
			"""
			tags[all]=3,4
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "1"
		And the response has a "results.0.id" property
		And the "results.0.id" property equals "99"
		And the response has a "results.0.status" property
		And the "results.0.status" property equals "draft"
		And the response has a "results.1.id" property
		And the "results.1.id" property equals "1"
		And the response has a "results.1.status" property
		And the "results.0.status" property equals "draft"
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
		Then the guzzle status code should be 200

	@search
	Scenario: Check status of Posts after update
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testadminuser"
		And that the request "query string" is:
			"""
			tags[any]=3,4
			"""
		When I request "/posts"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		And the "count" property equals "2"
		And the response has a "results.0.id" property
		And the "results.0.id" property equals "99"
		And the response has a "results.0.status" property
		And the "results.0.status" property equals "draft"
		And the response has a "results.1.id" property
		And the "results.1.id" property equals "1"
		And the response has a "results.1.status" property
		And the "results.0.status" property equals "draft"
		Then the guzzle status code should be 200