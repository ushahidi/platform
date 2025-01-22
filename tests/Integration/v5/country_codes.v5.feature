@resetFixture @countrycodes-v5
Feature: Country Code v5 endpoints tests
    Scenario: Properly authenticated user can get v5/country_codes
        Given that I want to get all "CountryCodes"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        When I request "/country-codes"
        Then the response is JSON
        And the response has a "total_count" property
        And the type of the "total_count" property is "numeric"
        And the "total_count" property equals "246"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Properly authenticated user can get v5/country_codes
        Given that I want to get all "CountryCodes"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        When I request "/country-codes?limit=10"
        Then the response is JSON
        And the response has a "total_count" property
        And the type of the "total_count" property is "numeric"
        And the "current_count" property equals "10"
        And the "total_count" property equals "246"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Basic User users cannot get v5/country_codes
        Given that I want to get all "CountryCodes"
        And that the oauth token is "testbasicuser"
        And that the api_url is "api/v5"
        When I request "/country-codes"
        Then the response is JSON
        Then the guzzle status code should be 403

    @resetFixture
    Scenario: Anonymous users cannot get v5/country_codes
        Given that I want to get all "CountryCodes"
        And that the oauth token is "testanon"
        And that the api_url is "api/v5"
        When I request "/country-codes"
        Then the response is JSON
        Then the guzzle status code should be 403

    @resetFixture
    Scenario: Properly authenticated user can get v5/country_codes/{id}
        Given that I want to find a "CountryCode"
        And that the oauth token is "testadminuser"
        And that the api_url is "api/v5"
        When I request "/country-codes/1"
        Then the response is JSON
        And the response has a "data.id" property
        And the type of the "data.id" property is "numeric"
        And the "data.id" property equals "1"
        Then the guzzle status code should be 200
