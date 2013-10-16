@media @oauth2Skip
Feature: Testing the Media API

    Scenario: Creating a new Media
        Given that I want to make a new "Media"
        And that the post field "caption" is "ihub"
        And that the post file "file" is "/Users/robbie/devel/Lamu/modules/UshahidiUI/media/images/avatar.png"
        When I request "/media"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: List all media
        Given that I want to get all "Media"
        When I request "/media"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "3"
        Then the guzzle status code should be 200