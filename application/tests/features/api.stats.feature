@statsFixture @oauth2Skip
Feature: Testing the Stats API
    
	@resetFixture
    Scenario: Getting a count
		Given that I want to count all "Stats"
		When I request "/stats"
		Then the response is JSON
		And the response has a "users" property
		And the response has a "posts" property
		And the response has a "messages" property
		Then the guzzle status code should be 200
