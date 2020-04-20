Feature: Gateway checker

    Scenario: normal API request should still work with enabled GW checker
        Given that I have enabled debug mode
        And that I want to get all "Configs"
        When I request "/config"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the guzzle status code should be 200

    Scenario Outline: get debug information with enabled GW checker
        Given that I have enabled debug mode
        And that I want to get all "Configs"
        When I request "/<endpoint>?gwcheck=<gwCheckValue>"
        Then the response is JSON
        And the response does not have a "count" property
        And the "api.name" property equals "ushahidi:platform:gwcheck"
        And the "api.version" property equals "0.1"
        And the "data._GET.gwcheck" property equals "<gwCheckValue>"
        And the "data._REQUEST.gwcheck" property equals "<gwCheckValue>"
        And the "data._POST" property is empty
        And the "data._SERVER.REQUEST_METHOD" property equals "GET"
        And the "data._SERVER.QUERY_STRING" property equals "gwcheck=<gwCheckValue>"
        And the "data._SERVER.HTTP_ACCEPT" property equals "application/json"
        And the "data._SERVER.SCRIPT_NAME" property contains "/index.php"
        And the "data._SERVER.REQUEST_URI" property equals "/api/v3/<endpoint>?gwcheck=<gwCheckValue>"
        #the following line might fail when running tests on Apache
        And the "data._SERVER.PATH_INFO" property equals "/api/v3/<endpoint>"
        And the "data._SERVER.HTTP_USER_AGENT" property contains "GuzzleHttp"
        And the "data._SERVER.DOCUMENT_ROOT" property contains "/httpdocs"
        And the "data._SERVER.SCRIPT_FILENAME" property contains "/httpdocs/index.php"
        And the "data._SERVER.HTTP_ACCEPT_CHARSET" property is empty
        And the "data._SERVER.HTTP_ACCEPT_ENCODING" property is empty
        And the "data._SERVER.HTTP_ACCEPT_LANGUAGE" property is empty
        And the "data._SERVER.HTTP_AUTHORIZATION" property is empty
        And the "data._SERVER.HTTP_CONNECTION" property is empty
        And the "data._SERVER.HTTP_ORIGIN" property is empty
        And the "data._SERVER.HTTP_REFERER" property is empty
        And the "data._SERVER.ORIG_PATH_INFO" property is empty
        And the guzzle status code should be 200
        Examples:
            | endpoint       | gwCheckValue |
            | config         | true         |
            | config         | TRUE         |
            | config         | 1            |
            | config         | anyString    |
            | config         | false        |
            | does-not-exist | true         |
            | does-not-exist | TRUE         |
            | does-not-exist | 1            |
            | does-not-exist | anyString    |
            | does-not-exist | false        |

    Scenario Outline: debug information should not be visible with disabled GW checker
        Given that I have disabled debug mode
        And that I want to get all "Configs"
        When I request "/config?gwcheck=<gwCheckValue>"
        Then the response is empty
        And the guzzle status code should be 204
        And the "X-Ushahidi-Platform-Install-Debug-Mode" header should be "off"
        Examples:
            | gwCheckValue |
            | true         |
            | TRUE         |
            | 1            |
            | anyString    |
            | false        |
