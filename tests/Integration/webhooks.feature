@webhooks
Feature: Testing the Webhook API

    Scenario: Create a webhook
        Given that I want to make a new "Webhook"
        And that the oauth token is "testadminuser"
        And that the request "data" is:
          """
          {
            "name":"test",
            "entity_type":"post",
            "event_type":"create",
            "shared_secret":"f2416258639b0584c909dd9cdb33db347577435797471c6b995a8af382cd8cd6",
            "url":"https://someplace.com/webhook/trigger"
          }
          """
        When I request "/webhooks"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: An anonymous user cannot create to a webhook
        Given that I want to make a new "Webhook"
        And that the oauth token is "testanon"
        And that the request "data" is:
            """
            {
                "name":"test",
                "entity_type":"post",
                "event_type":"create",
                "shared_secret":"f2416258639b0584c909dd9cdb33db347577435797471c6b995a8af382cd8cd6",
                "url":"https://someplace.com/webhook/trigger"
            }
            """
        When I request "/webhooks"
        Then the guzzle status code should be 403

    Scenario: Deleting a webhook
        Given that I want to delete a "Webhook"
        And that the oauth token is "testadminuser"
        And that its "id" is "2"
        When I request "/webhooks"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "2"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing Webhooks for a user
        Given that I want to get all "Webhooks"
        And that the oauth token is "testadminuser"
        And that the request "query string" is:
            """
                user=0
            """
        When I request "/webhooks"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "4"
        Then the guzzle status code should be 200

#    @resetFixture
#        Given that I want to update a "Post"
#        And that its "id" is "1"
#        And that the request "X-Ushahidi-Signature" header is "PqAl0200sE/hGYgGVyKis24c9p8RjYoLk9iMVxX3llk="
#        And that the request "data" is:
#          """
#          {
#            "id":"1",
#            "title": "Update test post title",
#            "webhook_uuid": "test-test-test",
#            "api_key" : "thisisatestapikeystring"
#          }
#          """
#        When I request "/webhooks/posts"
#        Then the response is JSON
#        Then the guzzle status code should be 200
#        Given that I want to find a "Post"
#        And that the oauth token is "testadminuser"
#        And that its "id" is "1"
#        When I request "/posts"
#        Then the response is JSON
#        And the response has a "id" property
#        And the response has a "title" property
#        And the "title" property equals "Update test post title"
