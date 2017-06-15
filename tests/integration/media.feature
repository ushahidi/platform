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
        And the "mime" property equals "image/png"
        And the response has a "original_file_url" property
        And the type of the "original_file_url" property is "string"
        And the "original_file_url" property contains "media/uploads/"
        And the response has a "original_width" property
        And the type of the "original_width" property is "numeric"
        And the response has a "original_height" property
        And the type of the "original_height" property is "numeric"
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
        And that its "id" is "2"
        When I request "/media"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "caption" property
        And the "caption" property equals "at sendai"
        And the response has a "mime" property
        And the type of the "mime" property is "string"
        And the "mime" property equals "image/jpeg"
        And the response has a "original_file_url" property
        And the type of the "original_file_url" property is "string"
        And the "original_file_url" property contains "/media/uploads/"
        And the response has a "original_width" property
        And the type of the "original_width" property is "numeric"
        And the "original_width" property equals "500"
        And the response has a "original_height" property
        And the type of the "original_height" property is "numeric"
        And the "original_height" property equals "600"
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

        Scenario: Finding a non-existent Media
        Given that I want to find a "Media"
        And that its "id" is "10"
        When I request "/media"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    @resetFixture
    Scenario: Listing all Media
        Given that I want to get all "Media"
        When I request "/media"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "4"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing All Media with limit and offset
        Given that I want to get all "Media"
        And that the request "query string" is:
            """
            limit=1&offset=1
            """
        When I request "/media"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "1"
        And the response has a "next" property
        And the response has a "prev" property
        And the response has a "curr" property
        And the "results.0.id" property equals "2"
        Then the guzzle status code should be 200

    Scenario: Fail to delete a non existent Media
        Given that I want to delete a "Media"
        And that its "id" is "10"
        When I request "/media"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Fail to create a new Media with size greater than limit
        Given that I want to make a new "Media"
        And that the post field "caption" is "ihub"
        And that the post file "file" is "tests/datasets/ushahidi/sample-large.png"
        When I request "/media"
        Then the response is JSON
        And the response has a "errors" property
        #Then the "errors.1.message" property equals "File type not supported. Please upload an image file."
        Then the "errors.1.message" property equals "The file size should be less than 1 MB"
        Then the guzzle status code should be 422

    @cdnEnabled
    Scenario: Media URLs are encoded correctly
        Given that I want to find a "Media"
        And that its "id" is "4"
        When I request "/media"
        Then the response is JSON
        And the response has a "original_file_url" property
        And the type of the "original_file_url" property is "string"
        And the "original_file_url" property contains "/some%20junk%20name.jpg"
        Then the guzzle status code should be 200
