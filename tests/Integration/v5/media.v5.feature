@media @oauth2Skip
Feature: Testing the Media API

    Scenario: Creating a new Media
        Given that I want to make a new "Media"
        And that the api_url is "api/v5"
        And that the post field "caption" is "ihub"
        And that the post file "file" is "tests/datasets/ushahidi/sample.png"
        When I request "/media"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the response has a "result.caption" property
        And the "result.caption" property equals "ihub"
        And the response has a "result.mime" property
        And the type of the "result.mime" property is "string"
        And the "result.mime" property equals "image/png"
        And the response has a "result.original_file_url" property
        And the type of the "result.original_file_url" property is "string"
        And the "result.original_file_url" property contains "storage/"
        And the response has a "result.original_width" property
        And the type of the "result.original_width" property is "numeric"
        And the response has a "result.original_height" property
        And the type of the "result.original_height" property is "numeric"
        # And the response has a "medium_file_url" property
        # And the type of the "medium_file_url" property is "string"
        # And the "medium_file_url" property contains "/imagefly/w800/uploads/"
        # And the response has a "medium_width" property
        # And the type of the "medium_width" property is "numeric"
        # And the "medium_width" property equals "800"
        # And the response has a "thumbnail_file_url" property
        # And the type of the "thumbnail_file_url" property is "string"
        # And the "thumbnail_file_url" property contains "/imagefly/w70/uploads/"
        # And the response has a "thumbnail_width" property
        # And the type of the "thumbnail_width" property is "numeric"
        # And the "thumbnail_width" property equals "70"
        Then the guzzle status code should be 200

    Scenario: Finding a Media
        Given that I want to find a "Media"
        And that the api_url is "api/v5"
        And that its "id" is "2"
        When I request "/media"
        Then the response is JSON
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the response has a "result.caption" property
        And the "result.caption" property equals "at sendai"
        And the response has a "result.mime" property
        And the type of the "result.mime" property is "string"
        And the "result.mime" property equals "image/jpeg"
        And the response has a "result.original_file_url" property
        And the type of the "result.original_file_url" property is "string"
        And the "result.original_file_url" property contains "/storage/"
        And the response has a "result.original_width" property
        And the type of the "result.original_width" property is "numeric"
        And the "result.original_width" property equals "500"
        And the response has a "result.original_height" property
        And the type of the "result.original_height" property is "numeric"
        And the "result.original_height" property equals "600"
        # And the response has a "result.medium_file_url" property
        # And the type of the "result.medium_file_url" property is "string"
        # And the "result.medium_file_url" property contains "/imagefly/w800/uploads/"
        # And the response has a "result.medium_width" property
        # And the type of the "result.medium_width" property is "numeric"
        # And the "result.medium_width" property equals "800"
        # And the response has a "result.thumbnail_file_url" property
        # And the type of the "result.thumbnail_file_url" property is "string"
        # And the "result.thumbnail_file_url" property contains "/imagefly/w70/uploads/"
        # And the response has a "result.thumbnail_width" property
        # And the type of the "result.thumbnail_width" property is "numeric"
        # And the "result.thumbnail_width" property equals "70"
        Then the guzzle status code should be 200

        Scenario: Finding a non-existent Media
        Given that I want to find a "Media"
        And that the api_url is "api/v5"
        And that its "id" is "10"
        When I request "/media"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Fail to delete a non existent Media
        Given that I want to delete a "Media"
        And that the api_url is "api/v5"
        And that its "id" is "10"
        When I request "/media"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    @resetFixture
    Scenario: Fail to create a new Media with size greater than limit
        Given that I want to make a new "Media"
        And that the api_url is "api/v5"
        And that the post field "caption" is "ihub"
        And that the post file "file" is "tests/datasets/ushahidi/sample-large.png"
        When I request "/media"
        Then the response is JSON
        And the response has a "errors" property
        #Then the "errors.failed_validations.0.error_messages.0" property equals "The file size should be less than 1 MB"
        Then the guzzle status code should be 422

    @cdnEnabled
    Scenario: Media URLs are encoded correctly
        Given that I want to find a "Media"
        And that the api_url is "api/v5"
        And that its "id" is "4"
        When I request "/media"
        Then the response is JSON
        And the response has a "result.original_file_url" property
        And the type of the "result.original_file_url" property is "string"
        And the "result.original_file_url" property contains "/some%20junk%20name.jpg"
        Then the guzzle status code should be 200
