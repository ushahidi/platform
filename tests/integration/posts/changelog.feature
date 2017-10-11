@postchangelog @oauth2Skip
Feature: Testing the Post Changelog API

    Scenario: Creating a new Changelog
        Given that I want to make a new "Changelog"
        And that the request "data" is:
        """
        {
            "post_id":99,
            "content":"Here is a post from Behat"
        }
        """
        When I request "/posts/99/changelog"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "content" property equals "Here is a post from Behat"
        And the "post_id" property equals "99"
        Then the guzzle status code should be 200

    Scenario: Creating a new Changelog with a non-existent Post
        Given that I want to make a new "Changelog"
        And that the request "data" is:
            """
            {
              "post_id":999999,
              "content":"Here is a post from Behat"
            }
            """
        When I request "/posts/999999/changelog"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Listing All Changelog for a Post
        Given that I want to get all "Changelogs"
        When I request "/posts/99/changelog"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the response has a "results" property

        Then the guzzle status code should be 200

    Scenario: Listing All Changelog
        Given that I want to get all "Changelogs"
        When I request "/posts/changelog"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        Then the guzzle status code should be 200
