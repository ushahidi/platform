Feature: CORS headers and preflight requests
    
    Scenario: Preflight config request
        Given that I want to make an OPTIONS request
        And that the request "Origin" header is "http://local.dev"
        And that the request "Access-Control-Request-Method" header is "POST"
        And that the request "Access-Control-Request-Headers" header is "Authorization, Content-Type, Accept"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the "Access-Control-Allow-Origin" header should be "http://local.dev"
        And the "Access-Control-Allow-Methods" header should exist
        And the "Access-Control-Allow-Headers" header should be "authorization, content-type, accept"

    Scenario: CORS config request to config
        Given that I want to make an OPTIONS request
        And that the request "Origin" header is "http://local.dev"
        When I request "/config"
        Then the guzzle status code should be 200
        And the "Access-Control-Allow-Origin" header should be "http://local.dev"

    Scenario: CORS config request to posts
        Given that I want to make an OPTIONS request
        And that the oauth token is "testbasicuser"
        And that the request "Origin" header is "http://local.dev"
        When I request "/config"
        Then the guzzle status code should be 200
        And the "Access-Control-Allow-Origin" header should be "http://local.dev"

    Scenario: Vanilla options request to config
        Given that I want to make an OPTIONS request
        When I request "/config"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has an "allowed_privileges" property

    Scenario: Vanilla options request to posts
        Given that I want to make an OPTIONS request
        And that the oauth token is "testbasicuser"
        When I request "/posts"
        Then the guzzle status code should be 200
        And the response is JSON
        And the response has an "allowed_privileges" property

