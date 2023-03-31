@datasources
    Feature: Testing DataSources API
        Scenario: List DataSources
            Given that I want to get all "DataSources"
            And that the oauth token is "testadminuser"
            And that the api_url is "api/v5"
            When I request "/datasources"
            Then the response is JSON
