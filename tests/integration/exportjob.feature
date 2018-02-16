@exportjobs
Feature: Testing the Export Job API

    Scenario: Create a export job
        Given that I want to make a new "ExportJob"
        And that the request "Authorization" header is "Bearer testadminuser"
        And that the request "data" is:
          """
          {
            "fields":"test",
            "filters":"test",
            "entity_type":"post"
          }
          """
        When I request "/exports/jobs"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: An anonymous user cannot create to an export job
        Given that I want to make a new "ExportJob"
        And that the request "Authorization" header is "Bearer testanon"
        And that the request "data" is:
            """
            {
              "fields":"test",
              "filters":"test",
              "entity_type":"post"
            }
            """
        When I request "/exports/jobs"
        Then the guzzle status code should be 403

#    @resetFixture
#    Scenario: Listing Export Jobs for a user
#        Given that I want to get all "ExportJob"
#        And that the request "Authorization" header is "Bearer testadminuser"
#        And that the request "query string" is:
#            """
#                user=0
#            """
#        When I request "/exports/jobs"
#        Then the response is JSON
#        And the response has a "count" property
#        And the type of the "count" property is "numeric"
#        And the "count" property equals "4"
#        Then the guzzle status code should be 200
