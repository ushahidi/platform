@oauth2Skip
Feature: Testing the HXL Tags API
    Scenario: List all HXL Tags
        Given that I want to get all "HXL Tags"
        When I request "/hxl/tags"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "44"
        And the response has a "results" property
        Then the guzzle status code should be 200
    Scenario: All HXL tags contain an id, url, tag_name, hxl_attributes and form_attribute_types
        Given that I want to get all "HXL Tags"
        When I request "/hxl/tags"
        Then the response is JSON
        And the response has a "results.0.id" property
        And the response has a "results.0.url" property
        And the response has a "results.0.tag_name" property
        And the response has a "results.0.hxl_attributes" property
        And the response has a "results.0.form_attribute_types" property
        Then the guzzle status code should be 200