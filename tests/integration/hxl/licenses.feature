Feature: Testing the HXL Licenses API
    Scenario: List all HXL Licenses
        Given that I want to get all "HXL Licenses"
        When I request "/hxl/licenses"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "total_count" property equals "9"
        And the response has a "result" property
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
        Then the guzzle status code should be 403
