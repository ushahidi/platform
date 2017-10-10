@post @oauth2Skip
Feature: Testing the Posts ChangeLog API

Scenario: Listing All Log Entries
  Given that I want to get all "ChangeLog"
  When I request "/posts/120/changelog"
  Then the response is JSON
  And the response has a "results" property
  And the response has a "results.1.content" property
  And the response has a "results.1.realname" property
  Then the guzzle status code should be 200


Scenario: Creating a manual Changelog entry
  Given that I want to make a new "Changelog"
  And that the request "data" is:
  """
  {
      "post_id":99,
      "content":"Here is a post from Behat",
  }
  """
  When I request "/posts/99/changelog"
  Then the response is JSON
  And the response has a "results" property
  And the response has a "results.1.content" property
  And the response has a "results.1.created" property
  And the "results.1.content" property equals "Here is a post from Behat"


Scenario: Testing logging of new Post

Scenario: Testing logging of updated simple Post

Scenario: Testing logging of updated Post w Tasks

Scenario: Testing logging of updated Post w Survey Items

Scenario: Testing logging of updated Post w misc items
