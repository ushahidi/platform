@post @oauth2Skip
Feature: Testing the Posts API

	Scenario: Listing All Posts as GeoJSON
		Given that I want to get all "Posts"
		When I request "/posts/geojson"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features" property count is "5"
		Then the guzzle status code should be 200

	Scenario: Find a Post as GeoJSON
		Given that I want to find a "Post"
		When I request "/posts/1/geojson"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features.2.properties.attribute_key" property equals "last_location_point"
		And the "features.2.geometry.type" property equals "Point"
		And the "features.0.properties.attribute_key" property equals "geometry_test"
		And the "features.0.geometry.type" property equals "MultiPolygon"
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

	Scenario: Listing 1 tile as GeoJSON
		Given that I want to get all "Posts"
		When I request "/posts/geojson/1/0/0"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features" property count is "0"
		# And the response has a "bbox" property
		And the "bbox.0" property equals "-180"
		And the "bbox.1" property equals "85.051128779807"
		And the "bbox.2" property equals "0"
		And the "bbox.3" property equals "0"
		Then the guzzle status code should be 200

	Scenario: Listing 1 tile as GeoJSON
		Given that I want to get all "Posts"
		When I request "/posts/geojson/1/1/0"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features" property count is "5"
		Then the guzzle status code should be 200

	Scenario: Return GeoJSON for bounding box
		Given that I want to get all "Posts"
		And that the request "query string" is:
		"""
			bbox=-2,-2,2,2
		"""
		When I request "/posts/geojson"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "bbox" property
		And the response has a "features" property
		And the "features" property count is "2"
		Then the guzzle status code should be 200

	Scenario: Get GeoJSON using center_point and within_km
		Given that I want to get all "Posts"
		And that the request "query string" is:
		"""
			center_point=1,1&within_km=1
		"""
		When I request "/posts/geojson"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features" property count is "2"
		Then the guzzle status code should be 200

	Scenario: Get GeoJSON for last_location_point attribute
		Given that I want to get all "Posts"
		And that the request "query string" is:
		"""
			include_attributes=second_point
		"""
		When I request "/posts/geojson"
		Then the response is JSON
		And the response has a "type" property
		And the response has a "features" property
		And the "features" property count is "1"
		Then the guzzle status code should be 200
