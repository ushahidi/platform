@oauth2Skip
Feature: Testing the DataProviders API

	Scenario: Listing All Data Providers
		Given that I want to get all "Dataproviders"
		When I request "/dataproviders"
		Then the response is JSON
		And the response has a "count" property
		And the type of the "count" property is "numeric"
		Then the guzzle status code should be 200

	Scenario: Get A Particular Data Provider
		Given that I want to get all "Dataprovider"
		When I request "/dataproviders/smssync"
		Then the response is JSON
		And the "id" property equals "smssync"
		And the "name" property equals "SMSSync"
		And the "options.secret.label" property equals "Secret"
		And the response has a "options.secret" property
		And the "options.secret.label" property equals "Secret"
		And the "options.secret.input" property equals "text"
		Then the guzzle status code should be 200

	Scenario: Search for Data Providers By Service Type
		Given that I want to get all "Dataproviders"
		And that the request "query string" is "type=sms"
		When I request "/dataproviders"
		Then the response is JSON
		And the type of the "count" property is "numeric"
		Then the guzzle status code should be 200

	Scenario: Geting A Non-Existent Data Provider Should Fail
		Given that I want to get all "Dataprovider"
		When I request "/dataproviders/doesnotexist"
		Then the guzzle status code should be 404
