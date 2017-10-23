@postlock @oauth2Skip
Feature: Testing Post Lock

    Scenario: Check Post Lock Not Set
        Given that I want to find a "Post"
	    And that its "id" is "1690"
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
        And the response does not have a "lock" property
        Then the guzzle status code should be 200
    
    Scenario: Get Post Lock
        Given that I want to update a "PostLock"
        And that the request "data" is:
            """
            {
                "post_id": 1691
            }
            """
        When I request "/posts/1691/lock"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "user" property
        And the response has a "expires" property
        And the type of the "expires" property is "numeric"
        And the response has a "post_id" property
        And the "post_id" property equals "1691"
        Then the guzzle status code should be 200
    
    Scenario: Check New Post Lock
        Given that I want to find a "Post"
	    And that its "id" is "1691"
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
        And the response has a "lock" property
        And the response has a "lock.user" property
        And the response has a "lock.user.id" property
        And the type of the "lock.user.id" property is "numeric"
        And the "lock.post_id" property equals "1691"
        Then the guzzle status code should be 200
    
    Scenario: Break a lock for a given post
        Given that I want to delete a "PostLock"
        When I request "/posts/1691/lock"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

     Scenario: Updating locked Post breaks lock
        Given that I want to find a "Post"
	    And that its "id" is "1692"
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
        And the response has a "lock" property
        And the response has a "lock.user" property
        And the response has a "lock.user.id" property
        And the type of the "lock.user.id" property is "numeric"
        And the "lock.post_id" property equals "1692"
        Then the guzzle status code should be 200
        Given that I want to update a "Post"
		And that the request "data" is:
			"""
			{
				"title":"Updated Test Post"
			}
			"""
		And that its "id" is "1692"
		When I request "/posts"
		Then the response is JSON
		And the response has a "id" property
		And the type of the "id" property is "numeric"
        And the response does not have a "lock" property
        Then the guzzle status code should be 200

