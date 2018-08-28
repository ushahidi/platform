@formStats
Feature: Testing the Form Stats
    @resetFixture
    Scenario: Getting the number of recipients who received an sms targeted survey
        Given that I want to count all "TargetedSurveyState"
        And that the oauth token is "testadminuser"
        When I request "/forms/6/stats"
        Then the response is JSON
        And the response has a "total_recipients" property
        And the type of the "total_recipients" property is "numeric"
        And the "total_recipients" property equals "1"
        And the response has a "total_responses" property
        And the type of the "total_responses" property is "numeric"
        And the "total_responses" property equals "0"
        And the response has a "total_messages_pending" property
        And the type of the "total_messages_pending" property is "numeric"
        And the "total_messages_pending" property equals "1"
        And the response has a "total_messages_sent" property
        And the type of the "total_messages_sent" property is "numeric"
        And the "total_messages_sent" property equals "0"
        And the response has a "total_response_recipients" property
        And the type of the "total_response_recipients" property is "numeric"
        And the "total_response_recipients" property equals "0"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Getting the number of recipients who received an sms targeted survey (1 invalidated contact)
        Given that I want to count all "TargetedSurveyState"
        And that the oauth token is "testadminuser"
        When I request "/forms/7/stats"
        Then the response is JSON
        And the response has a "total_recipients" property
        And the type of the "total_recipients" property is "numeric"
        And the "total_recipients" property equals "2"
        And the response has a "total_responses" property
        And the type of the "total_responses" property is "numeric"
        And the "total_responses" property equals "0"
        And the response has a "total_messages_pending" property
        And the type of the "total_messages_pending" property is "numeric"
        And the "total_messages_pending" property equals "2"
        And the response has a "total_messages_sent" property
        And the type of the "total_messages_sent" property is "numeric"
        And the "total_messages_sent" property equals "3"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Getting the number of responses received in survey 8
        Given that I want to count all "TargetedSurveyState"
        And that the oauth token is "testadminuser"
        When I request "/forms/8/stats"
        Then the response is JSON
        And the response has a "total_recipients" property
        And the type of the "total_recipients" property is "numeric"
        And the "total_recipients" property equals "2"
        And the response has a "total_responses" property
        And the type of the "total_responses" property is "numeric"
        And the "total_responses" property equals "1"
        And the response has a "total_messages_pending" property
        And the type of the "total_messages_pending" property is "numeric"
        And the "total_messages_pending" property equals "1"
        And the response has a "total_messages_sent" property
        And the type of the "total_messages_sent" property is "numeric"
        And the "total_messages_sent" property equals "3"
        And the response has a "total_response_recipients" property
        And the type of the "total_response_recipients" property is "numeric"
        And the "total_response_recipients" property equals "1"
        Then the guzzle status code should be 200

    @resetFixture
    Scenario: Getting the number of posts by data source for survey 1
        And that the oauth token is "testadminuser"
        When I request "/forms/1/stats"
        Then the response is JSON
        And the response has a "total_by_data_source" property
        And the type of the "total_by_data_source" property is "array"
        And the "total_by_data_source.sms" property equals "2"
        And the "total_by_data_source.email" property equals "1"
        And the "total_by_data_source.twitter" property equals "1"
        And the "total_by_data_source.web" property equals "13"
        And the "total_by_data_source.all" property equals "17"
