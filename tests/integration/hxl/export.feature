@hxlEnabled
Scenario: Create a export job with send_to_browser=true
Given that I want to make a new "ExportJob"
And that the oauth token is "testadminuser"
And that the request "data" is:
"""
          {
            "send_to_browser": true,
            "send_to_hdx": true,
            "fields":"test",
            "filters":
            {
              "status" : ["published","draft"],
              "has_location" : "all",
              "orderby" : "created",
              "order" : "desc",
              "order_unlocked_on_top" : "true",
              "source" : ["sms","twitter","web","email"]
            },
            "entity_type":"post"
          }
          """
When I request "/exports/jobs"
Then the response is JSON
And the response has a "errors" property
And the "errors.1.title" property equals "send_to_hdx should be false when send_to_browser is true"
Then the guzzle status code should be 422