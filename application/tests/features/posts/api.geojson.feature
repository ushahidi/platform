@post @oauth2Skip
Feature: Testing the Posts API

	Scenario: Listing All Posts as GeoJSON
		Given that I want to get all "Posts"
		When I request "/posts/geojson"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features.2.geometry.coordinates.0" property equals "10.123"
		And the "features.2.geometry.coordinates.1" property equals "26.213"
		Then the guzzle status code should be 200
	
	Scenario: Find a Post as GeoJSON
		Given that I want to find a "Post"
		When I request "/posts/1/geojson"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features.0.geometry.geometries.0.type" property equals "Point"
		And the "features.0.geometry.geometries.1.type" property equals "MultiPolygon"
		Then the guzzle status code should be 200
	
	Scenario: Find a Post as GeoJSON
		Given that I want to find a "Post"
		When I request "/posts/99/geojson"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features.0.geometry.coordinates.0" property equals "11.123"
		And the "features.0.geometry.coordinates.1" property equals "24.213"
		Then the guzzle status code should be 200

	Scenario: Listing All Posts as GeoJSON
		Given that I want to get all "Posts"
		When I request "/posts/geojson/1/0/0"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features" property count is "0"
		And the response has a "bbox" property
		And the "bbox.0" property equals "-180"
		And the "bbox.1" property equals "85.051128779807"
		And the "bbox.2" property equals "0"
		And the "bbox.3" property equals "0"
		Then the guzzle status code should be 200

	Scenario: Listing All Posts as GeoJSON
		Given that I want to get all "Posts"
		When I request "/posts/geojson/1/1/0"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features" property count is "4"
		Then the guzzle status code should be 200