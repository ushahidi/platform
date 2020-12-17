@post @rolesEnabled
Feature: Testing the Posts API
  @resetFixture @search
  Scenario: Getting all posts as an admin
    Given that I want to get all "Posts"
    And that the api_url is "api/v5"
    And that the oauth token is "testbasicuser"
    When I request "/posts"
    Then the response is JSON
    And the response has a "count" property
    And the type of the "count" property is "numeric"
    And the "count" property equals "20"
    And the "meta.total" property equals "31"
    Then the guzzle status code should be 200

  @resetFixture @search
  Scenario: Getting all published posts for anonymous user
    Given that I want to get all "Posts"
    And that the api_url is "api/v5"
    When I request "/posts"
    Then the response is JSON
    And the response has a "count" property
    And the type of the "count" property is "numeric"
    And the "count" property equals "19"
    And the "meta.total" property equals "19"
    Then the guzzle status code should be 200
  @resetFixture @search
  Scenario: Listing all posts page=2 should return 3 posts for user 1
    Given that I want to get all "Posts"
    And that the api_url is "api/v5"
    And that the oauth token is "testbasicuser"
    And that the request "query string" is:
			"""
			page=2
			"""
    When I request "/posts"
    Then the response is JSON
    And the response has a "count" property
    And the type of the "count" property is "numeric"
    And the "count" property equals "3"
    Then the guzzle status code should be 200

  @get
  Scenario: Finding a Post
    Given that I want to find a "Post"
    And that the api_url is "api/v5"
    And that the oauth token is "testbasicuser"
    And that its "id" is "1"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.title" property equals "Test post"
    And the "result.content" property equals "Testing post"
    And the "result.form_id" property equals "1"
    And the response has a "result.post_content" property
    And the response has a "result.post_content.0.fields.1" property
    Then the guzzle status code should be 200

  @get
  Scenario: Finding a non-existent Post
    Given that I want to find a "Post"
    And that the api_url is "api/v5"
    And that the oauth token is "testbasicuser"
    And that its "id" is "35"
    When I request "/posts"
    Then the response is JSON
    And the response has a "error" property
    Then the guzzle status code should be 404

  @get @postsAnon
  Scenario: View post with restricted data as normal users limits info
    Given that I want to find a "Post"
    And that the api_url is "api/v5"
    And that the oauth token is "testbasicuser"
    And that its "id" is "1690"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.title" property equals "Post published to members"
    And the response has a "result.post_content.0.fields" property
    And the "result.post_content.0.fields.0.value" property is empty
    And the "result.post_content.0.fields.0.response_private" property equals "1"
    And the response does not have a "result.post_content.1" property
    And the "result.post_date" property equals "2014-09-29T00:00:00+0000"
    And the "result.created" property equals "2014-09-29T00:00:00+0000"
    And the "result.updated" property is empty
    And the "result.user_id" property is empty
    And the "result.author_email" property is empty
    And the "result.author_realname" property is empty
    Then the guzzle status code should be 200

  @get
  Scenario: View post with restricted data as admin gets full info
    Given that I want to find a "Post"
    And that the api_url is "api/v5"
    And that the oauth token is "testadminuser"
    And that its "id" is "1690"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.title" property equals "Post published to members"
    And the response has a "result.post_content" property
    And the "result.post_content.1.fields.3.value.value.lat" property equals "26.2135"
    And the "result.post_content.1.fields.3.value.value.lon" property equals "10.1235"
    And the "result.post_content.1.fields.4.value.value" property equals "2014-09-29T15:11:46+0000"
    And the "result.post_date" property equals "2014-09-29T14:10:16+0000"
    And the "result.created" property equals "2014-09-29T21:10:16+0000"
    And the "result.updated" property is empty
    And the "result.user_id" property equals "3"
    And the "result.author_email" property equals "test@ushahidi.com"
    And the "result.author_realname" property equals "Test Name"
    Then the guzzle status code should be 200
