@post @oauth2Skip
Feature: Testing the Translations API

    @resetFixture
    Scenario: Listing All Translations
        Given that I want to get all "Translations"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "count" property
        And the type of the "count" property is "numeric"
        And the "count" property equals "2"
        Then the guzzle status code should be 200

    Scenario: Listing All Translations on a non-existent Post
        Given that I want to get all "Translations"
        When I request "/posts/999/translations"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Finding a Translation
        Given that I want to find a "Translation"
        And that its "id" is "106"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Finding a Translation with locale
        Given that I want to find a "Translation"
        And that its "id" is "fr_FR"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "locale" property equals "fr_fr"
        Then the guzzle status code should be 200

    Scenario: Finding a non-existent Translation
        Given that I want to find a "Translation"
        And that its "id" is "35"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Fail to find a Report as Translation
        Given that I want to find a "Translation"
        And that its "id" is "1"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Fail to find a Revision as Translation
        Given that I want to find a "Translation"
        And that its "id" is "107"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Fail to find a Revision as Translation
        Given that I want to find a "Translation"
        And that its "id" is "fr_FR"
        When I request "/posts/106/translations"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Fail to find Translation through Posts api
        Given that I want to find a "Translation"
        And that its "id" is "106"
        When I request "/posts"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Finding a Revision of a  Translation
        Given that I want to find a "Revision"
        And that its "id" is "107"
        When I request "/posts/106/revisions"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        Then the guzzle status code should be 200

    Scenario: Creating a new Translation
        Given that I want to make a new "Translation"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test translation",
                "content": "Some description",
                "status": "published",
                "locale":"de_DE",
                "values": {
                    "test_varchar": ["testing"],
                    "last_location": ["blah"]
                },
                "tags": ["disaster"]
            }
            """
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "title" property
        And the "title" property equals "Test translation"
        And the "type" property equals "translation"
        Then the guzzle status code should be 200

    Scenario: Creating a new Translation with same lang as original
        Given that I want to make a new "Translation"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test translation",
                "content": "Some description",
                "status": "published",
                "locale":"en_US",
                "values": {
                    "test_varchar": ["testing"],
                    "last_location": ["blah"]
                },
                "tags": ["disaster"]
            }
            """
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    Scenario: Creating a new Translation with same lang as existing Translation
        Given that I want to make a new "Translation"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test translation",
                "content": "Some description",
                "status": "published",
                "locale":"fr_FR",
                "values": {
                    "test_varchar": ["testing"],
                    "last_location": ["blah"]
                },
                "tags": ["disaster"]
            }
            """
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 422

    Scenario: Updating a Translation
        Given that I want to update a "Translation"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test translation updated",
                "content": "Some description",
                "status": "published",
                "locale":"fr_FR",
                "values": {
                    "test_varchar": ["testing"],
                    "last_location": ["blah"]
                },
                "tags": ["disaster"]
            }
            """
        And that its "id" is "106"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the response has a "title" property
        And the "title" property equals "Test translation updated"
        Then the guzzle status code should be 200

    Scenario: Updating a Translation with locale url
        Given that I want to update a "Translation"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test translation updated2",
                "content": "Some description",
                "status": "published",
                "values": {
                    "test_varchar": ["testing"],
                    "last_location": ["blah"]
                },
                "tags": ["disaster"]
            }
            """
        And that its "id" is "fr_FR"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "id" property
        And the type of the "id" property is "numeric"
        And the "parent.id" property equals "105"
        And the response has a "title" property
        And the "title" property equals "Test translation updated2"
        Then the guzzle status code should be 200

    Scenario: Updating a non-existent Translation
        Given that I want to update a "Translation"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test translation updated",
                "content": "Some description",
                "status": "published",
                "locale":"de_DE",
                "values": {
                    "test_varchar": ["testing"],
                    "last_location": ["blah"]
                },
                "tags": ["disaster"]
            }
            """
        And that its "id" is "40"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Updating a Translation with non-existent Post
        Given that I want to update a "Translation"
        And that the request "data" is:
            """
            {
                "form": 1,
                "title": "Test translation updated",
                "content": "Some description",
                "status": "published",
                "locale":"de_DE",
                "values": {
                    "test_varchar": ["testing"],
                    "last_location": ["blah"]
                },
                "tags": ["disaster"]
            }
            """
        And that its "id" is "106"
        When I request "/posts/35/translations"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404

    Scenario: Deleting a Translation
        Given that I want to delete a "Translation"
        And that its "id" is "106"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "id" property
        Then the guzzle status code should be 200

    Scenario: Fail to delete a non existent Translation
        Given that I want to delete a "Translation"
        And that its "id" is "200"
        When I request "/posts/105/translations"
        Then the response is JSON
        And the response has a "errors" property
        Then the guzzle status code should be 404
