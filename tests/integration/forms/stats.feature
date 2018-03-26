Feature: Testing the Form Stats
    @resetFixture
    Scenario: Getting the number of recipients who received an sms targeted survey
        Given that I want to count all "ContactPostState"
        And that the request "Authorization" header is "Bearer testadminuser"
        When I request "/forms/6/stats"
        Then the response is JSON
        And the response has a "total_recipients" property
        And the type of the "total_recipients" property is "numeric"
        And the response has a "total_responses" property
        And the type of the "total_responses" property is "numeric"
        Then the guzzle status code should be 200
    