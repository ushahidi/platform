@post @rolesEnabled
Feature: Testing the Posts API with Statuses
  @create @rolesEnabled
  Scenario: Creating a new Post on unmoderated form
  Given that I want to make a new "Post"
    And that the oauth token is "testadminuser"
    And that the api_url is "api/v5"
    And that the request "data" is:
      """
        {
            "title": "A title",
            "content": "A description",
            "locale": "en_US",
            "post_content": [
                {
                    "id": 1,
                    "type": "post",
                    "fields": [
                        {
                            "id": 7,
                            "type": "varchar",
                            "value": {
                                "value": "Uruguay"
                            }
                        }
                      ]
                }
            ],
            "completed_stages": [
                2,
                3
            ],
            "post_date": "2020-06-24T07:04:07.897Z",
            "enabled_languages": {},
            "base_language": "",
            "type": "report",
            "form_id": 1
        }
      """
    When I request "/posts"
    Then the guzzle status code should be 201
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.status" property equals "published"


  @create @rolesEnabled
  Scenario: Creating a new Post on moderated form
  Given that I want to make a new "Post"
    And that the oauth token is "testadminuser"
    And that the api_url is "api/v5"
    And that the request "data" is:
      """
        {
            "title": "A title",
            "content": "A description",
            "locale": "en_US",
            "post_content": [],
            "completed_stages": [],
            "enabled_languages": {},
            "base_language": "",
            "type": "report",
            "form_id": 4
        }
      """
    When I request "/posts"
    Then the guzzle status code should be 201
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.status" property equals "draft"
