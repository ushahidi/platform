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

    Scenario: Anonymous users can't see Changelog
        Given that I want to get all "Changelogs"
        And that the request "Authorization" header is "Bearer testanon"
        When I request "/posts/99/changelog"
        Then the guzzle status code should be 403

    Scenario: Anonymous users can't create a Changelog entry
        Given that I want to make a new "Changelog"
        And that the request "data" is:
        """
        {
            "post_id":99,
            "content":"Here is a post from Behat"
        }
        """
        And that the request "Authorization" header is "Bearer testanon"
        When I request "/posts/99/changelog"
        Then the response is JSON
        Then the guzzle status code should be 403

#    Scenario: Logged-in user should see Changelog   #  updated per requirements discussion
#        Given that I want to get all "Changelogs"
#        And that the request "Authorization" header is "Bearer testbasicuser"
#        When I request "/posts/99/changelog"
#        Then the guzzle status code should be 200

    Scenario: Logged-in user should not see Changelog  # updated per requirements discussion
        Given that I want to get all "Changelogs"
        And that the request "Authorization" header is "Bearer testanon"
        When I request "/posts/99/changelog"
        Then the guzzle status code should be 403

    Scenario: Standard logged-in user can't create a Changelog entry
        Given that I want to make a new "Changelog"
        And that the request "data" is:
        """
        {
            "post_id":99,
            "content":"Here is a post from Behat"
        }
        """
        And that the request "Authorization" header is "Bearer testbasicuser"
        When I request "/posts/99/changelog"
        Then the response is JSON
        Then the guzzle status code should be 403

    Scenario: Managers should see Changelog
        Given that I want to get all "Changelogs"
        And that the request "Authorization" header is "Bearer testmanager"
        When I request "/posts/99/changelog"
        Then the guzzle status code should be 200

    Scenario: Managers should be able to create a Changelog entry
        Given that I want to make a new "Changelog"
        And that the request "data" is:
        """
        {
            "post_id":99,
            "content":"Here is a post from Behat"
        }
        """
        And that the request "Authorization" header is "Bearer testmanager"
        When I request "/posts/99/changelog"
        When I request "/posts/99/changelog"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "content" property equals "Here is a post from Behat"
        And the "post_id" property equals "99"
        Then the guzzle status code should be 200

    Scenario: Adding a post to collections creates an entry in Changelog
        Given that I want to make a new "Post"
        And that the request "data" is:
            """
            {
                "id":1692
            }
            """
        When I request "/collections/1/posts/"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "1692"
        Then the guzzle status code should be 200
        Given that I want to get all "Changelogs"
        When I request "/posts/1692/changelog"
        Then the response is JSON
        And the response has a "results" property
        And the response has a "results.0.post_id" property
        And the type of the "results.0.post_id" property is "numeric"
        And the "results.0.post_id" property equals "1692"
        And the response has a "results.0.content" property
        And the "results.0.content" property equals "Added post to collection."
        Then the guzzle status code should be 200

@updatingpostsforchangelog

    Scenario: Updating a Post title create a Changelog entry
      Given that I want to update a "Post"
      And that the request "data" is:
        """
        {
          "title":"This is a recently updated title.",
          "values":
          {
            "last_location":["Atlanta"]
          }
        }
        """
      And that its "id" is "1"
      When I request "/posts"
      Then the response is JSON
      And the response has a "id" property
      Given that I want to get all "Changelogs"
      When I request "/posts/1/changelog"
      Then the response is JSON
      And the response has a "results" property
      And the response has a "results.0.post_id" property
      And the type of the "results.0.post_id" property is "numeric"
      And the "results.0.post_id" property equals "1"
      And the response has a "results.0.content" property
      # TODO: for the moment, these are all being concatenated into one text blob. Worth reconsidering?
      And the "results.0.content" property contains "Changed title to"
      And the "results.0.content" property contains "This is a recently updated title."
      Then the guzzle status code should be 200

  Scenario: Updating a Post should create a Changelog entry
    Given that I want to update a "Post"
    And that the request "data" is:
      """
      {
        "form":1,
        "title":"Updated Test Post",
        "type":"report",
        "status":"published",
        "locale":"en_US",
        "values":
        {
          "full_name":["David Kobia"],
          "description":["Skinny, homeless Kenyan last seen in the vicinity of the greyhound station"],
          "date_of_birth":[],
          "missing_date":["2012/09/25"],
          "last_location":["Chicago"],
          "last_location_point":[
            {
              "lat": 33.755,
              "lon": -85.39
            }
          ],
          "missing_status":["believed_missing"]
        },
        "tags":["disaster","explosion"],
        "completed_stages":[1]
      }
      """
    And that its "id" is "1"
    When I request "/posts"
    Then the response is JSON
    And the response has a "id" property
    Given that I want to get all "Changelogs"
    When I request "/posts/1/changelog"
    Then the response is JSON
    And the response has a "results" property
    And the response has a "results.1.post_id" property
    And the type of the "results.1.post_id" property is "numeric"
    And the "results.0.post_id" property equals "1"
    And the response has a "results.1.content" property
    # TODO: for the moment, these are all being concatenated into one text blob. Worth reconsidering?
    And the "results.1.content" property contains "Changed title to"
    And the "results.1.content" property contains "Updated Test Post"
    And the "results.1.content" property contains "description"
    And the "results.1.content" property contains "last-location"
    And the "results.1.content" property contains "full-name"
    Then the guzzle status code should be 200
