@postdataexports
Feature: Testing the Post Data Exports API

    Scenario: Create a postdataexport
        Given that I want to make a new "PostDataExport"
        And that the request "Authorization" header is "Bearer testadminuser"
        And that the request "data" is:
          """
          {
            "filter": {
					"q":"zombie"
            }
          }
          """
        When I request "/postdataexports"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: An anonymous user cannot create to a psotdataexport
        Given that I want to make a new "PostDataExport"
        And that the request "Authorization" header is "Bearer testanon"
        And that the request "data" is:
            """
            {
                "filter": {
                        "q":"zombie"
                }
            }
            """
        When I request "/postdataexports"
        Then the guzzle status code should be 400

    Scenario: Deleting a postdataexport
        Given that I want to delete a "PostDataExport"
        And that the request "Authorization" header is "Bearer testadminuser"
        And that its "id" is "2"
        When I request "/postdataexports"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "id" property equals "2"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Listing PostDataExports for a user
        Given that I want to get all "PostDataexports"
        And that the request "Authorization" header is "Bearer testadminuser"
        And that the request "query string" is:
            """
                user=0
            """
        When I request "/postdataexports"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "4"
        Then the guzzle status code should be 200
