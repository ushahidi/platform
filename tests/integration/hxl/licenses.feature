@hxl @resetFixture
Feature: Testing the HXL Licenses API
    Scenario: List all HXL Licenses
        Given that I want to get all "HXL Licenses"
        And that the oauth token is "testadminuser"
        When I request "/hxl/licenses"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "8"
        And the response has a "results" property
        Then the guzzle status code should be 200

    Scenario: Find one HXL License by code
        Given that I want to find a "HXL License"
        And that the oauth token is "testadminuser"
        And that the request "query string" is:
			"""
			code=cc-by-igo
			"""
        When I request "/hxl/licenses"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        And the response has a "results" property
        And the response has a "results.0.name" property
        And the response has a "results.0.code" property
        And the "results.0.code" property equals "cc-by-igo"
        And the "results.0.name" property equals "Creative Commons Attribution for Intergovernmental Organisations"
        Then the guzzle status code should be 200

    Scenario: Find one HXL License by name
        Given that I want to find a "HXL License"
        And that the oauth token is "testadminuser"
        And that the request "query string" is:
			"""
			name=Creative Commons Attribution for Intergovernmental Organisations
			"""
        When I request "/hxl/licenses"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        And the response has a "results" property
        And the response has a "results.0.name" property
        And the response has a "results.0.code" property
        And the "results.0.code" property equals "cc-by-igo"
        And the "results.0.name" property equals "Creative Commons Attribution for Intergovernmental Organisations"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Basic User users cannot get HXL Licenses
        Given that I want to get all "HXLLicenses"
        And that the oauth token is "testbasicuser"
        When I request "/hxl/licenses"
        Then the response is JSON
        Then the guzzle status code should be 403

    @resetFixture
    Scenario: Anonymous users cannot get HXL Licenses
        Given that I want to get all "HXLLicenses"
        And that the oauth token is "testanon"
        When I request "/hxl/licenses"
        Then the response is JSON
        Then the guzzle status code should be 403

    @resetFixture
    Scenario: Not using a token results in a 401
        Given that I want to get all "HXLLicenses"
        When I request "/hxl/licenses"
        Then the response is JSON
        Then the guzzle status code should be 401
