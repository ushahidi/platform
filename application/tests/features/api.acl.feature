Feature: API Access Control Layer

	Scenario: Anonymous user can access public posts
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testanon"
		When I request "/posts"
		Then the guzzle status code should be 200
		And the response is JSON
		And the "count" property equals "10"
		
	Scenario: All users can view public posts
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testbasicuser2"
		When I request "/posts"
		Then the guzzle status code should be 200
		And the response is JSON
		And the "count" property equals "10"
		
	Scenario: User can view public and own private posts in collection
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testbasicuser"
		When I request "/posts"
		Then the guzzle status code should be 200
		And the response is JSON
		And the "count" property equals "11"
		
	Scenario: Admin can view all posts in collection
		Given that I want to get all "Posts"
		And that the request "Authorization" header is "Bearer testadminuser"
		When I request "/posts"
		Then the guzzle status code should be 200
		And the response is JSON
		And the "count" property equals "13"
	
	Scenario: Admin user can view private posts
		Given that I want to find a "Post"
		And that its "id" is "111"
		And that the request "Authorization" header is "Bearer testadminuser"
		When I request "/posts"
		Then the guzzle status code should be 200
		And the response is JSON
		And the response has an "id" property
	
	Scenario: User can view their own private posts
		Given that I want to find a "Post"
		And that its "id" is "111"
		And that the request "Authorization" header is "Bearer testbasicuser"
		When I request "/posts"
		Then the guzzle status code should be 200
		And the response is JSON
		And the response has an "id" property
	
	Scenario: Users can not view private posts
		Given that I want to find a "Post"
		And that its "id" is "111"
		And that the request "Authorization" header is "Bearer testbasicuser2"
		When I request "/posts"
		Then the guzzle status code should be 403
		And the response is JSON
		And the response has an "errors" property
	
	Scenario: Users can edit their own posts
		Given that I want to update a "Post"
		And that its "id" is "110"
		And that the request "Authorization" header is "Bearer testbasicuser"
		And that the request "data" is:
		"""
		{
			"form_id": 1,
			"title": "Test editing own post",
			"description": "testing post for oauth",
			"status": "published",
			"locale": "en_us"
		}
		"""
		When I request "/posts"
		Then the guzzle status code should be 200
		And the response is JSON
		And the response has an "id" property
	
	Scenario: Anonymous users can create posts
		Given that I want to make a new "Post"
		And that the request "Authorization" header is "Bearer testanon"
		And that the request "data" is:
		"""
		{
			"form_id": 1,
			"title": "Test creating anonymous post",
			"description": "testing post for oauth",
			"status": "published",
			"locale": "en_us"
		}
		"""
		When I request "/posts"
		Then the guzzle status code should be 200
		And the response is JSON
		And the response has an "id" property
	
	Scenario: Anonymous users can not edit posts
		Given that I want to update a "Post"
		And that the request "Authorization" header is "Bearer testanon"
		And that its "id" is "110"
		And that the request "data" is:
		"""
		{
			"form_id": 1,
			"title": "Test post",
			"description": "testing post for oauth",
			"status": "published"
		}
		"""
		When I request "/posts"
		Then the guzzle status code should be 403
	
	Scenario: Anonymous users can not view private posts
		Given that I want to find a "Post"
		And that the request "Authorization" header is "Bearer testanon"
		And that its "id" is "111"
		When I request "/posts"
		Then the guzzle status code should be 403
