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
    Then the guzzle status code should be 200
