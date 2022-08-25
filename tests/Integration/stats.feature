@statsFixture @oauth2Skip
Feature: Testing the Stats API

	# Scenario: Getting a count
	# 	Given that I want to count all "Stats"
	# 	When I request "/stats"
	# 	Then the response is JSON
	# 	And the response has a "users" property
	# 	And the response has a "posts" property
	# 	And the response has a "messages" property
	# 	Then the guzzle status code should be 200

	Scenario: Getting posts count by tag
		Given that I want to count all "Stats"
		And that the request "query string" is:
			"""
			group_by=tags
			"""
		When I request "/posts/stats"
		Then the response is JSON
		And the response has a "totals" property
		And the "group_by" property equals "tags"
		Then the guzzle status code should be 200

	Scenario: Getting posts count by form
		Given that I want to count all "Stats"
		And that the request "query string" is:
			"""
			group_by=form
			"""
		When I request "/posts/stats"
		Then the response is JSON
		And the response has a "totals" property
		And the "group_by" property equals "form"
		Then the guzzle status code should be 200

	Scenario: Getting posts count by form
		Given that I want to count all "Stats"
		And that the request "query string" is:
			"""
			group_by=attribute&attribute_key=test_varchar
			"""
		When I request "/posts/stats"
		Then the response is JSON
		And the response has a "totals" property
		And the "group_by" property equals "attribute"
		Then the guzzle status code should be 200

	Scenario: Getting posts count by form
		Given that I want to count all "Stats"
		And that the request "query string" is:
			"""
			timeline=1&timeline_interval=900
			"""
		When I request "/posts/stats"
		Then the response is JSON
		And the response has a "totals" property
		Then the guzzle status code should be 200

	Scenario: Getting posts count by form
		Given that I want to count all "Stats"
		And that the request "query string" is:
			"""
			timeline=1&timeline_interval=900&group_by=tags
			"""
		When I request "/posts/stats"
		Then the response is JSON
		And the response has a "totals" property
		And the "group_by" property equals "tags"
		Then the guzzle status code should be 200
