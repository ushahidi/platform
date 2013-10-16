@media @oauth2Skip
Feature: Testing the Media API

    Scenario: Creating a new Media
        Given that I want to make a new "Media"
        And that the post field "caption" is "ihub"
        And that the post file "file" is "tests/datasets/ushahidi/sample.png"
        When I request "/media"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "caption" property
        And the "caption" property equals "ihub"
        And the response has a "mime" property
        And the type of the "mime" property is "string"
        And the "mime" property equals "image/jpeg"
        And the response has a "original_file_url" property
        And the type of the "original_file_url" property is "string"
        And the response has a "original_width" property
        And the type of the "original_width" property is "numeric"
        And the response has a "original_height" property
        And the type of the "original_height" property is "numeric"
        And the response has a "medium_file_url" property
        And the type of the "medium_file_url" property is "string"
        And the response has a "medium_width" property
        And the type of the "medium_width" property is "numeric"
        And the response has a "thumbnail_file_url" property
        And the type of the "thumbnail_file_url" property is "string"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: List all media
        Given that I want to get all "Media"
        When I request "/media"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "3"
        Then the guzzle status code should be 200