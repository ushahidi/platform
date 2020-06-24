@post @rolesEnabled
Feature: Testing the Posts API
  @create @rolesEnabled
  Scenario: Creating a new Post
    Given that I want to make a new "Post"
    And that the oauth token is "testmanager"
    And that the api_url is "api/v4"
    And that the request "data" is:
			"""
			{
              "title": "A title",
              "description": "",
              "locale": "en_US",
              "post_content": [
                  {
                      "id": 1,
                      "form_id": 1,
                      "label": "Main",
                      "priority": 1,
                      "required": 0,
                      "type": "post",
                      "description": null,
                      "show_when_published": 1,
                      "task_is_internal_only": 0,
                      "fields": [
                          {
                              "id": 1,
                              "key": "test_varchar",
                              "label": "Test varchar",
                              "instructions": null,
                              "input": "text",
                              "type": "varchar",
                              "required": 0,
                              "default": null,
                              "priority": 1,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": "MY VARCHAR"
                              }
                          },
                          {
                              "id": 2,
                              "key": "test_point",
                              "label": "Test point",
                              "instructions": null,
                              "input": "location",
                              "type": "point",
                              "required": 0,
                              "default": null,
                              "priority": 1,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": {
                                      "lat": -18.892817463050697,
                                      "lon": 22.840418464728486
                                  }
                              }
                          },
                          {
                              "id": 3,
                              "key": "full_name",
                              "label": "Full Name",
                              "instructions": null,
                              "input": "text",
                              "type": "varchar",
                              "required": 0,
                              "default": null,
                              "priority": 1,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": "A full name"
                              }
                          },
                          {
                              "id": 4,
                              "key": "description",
                              "label": "Description",
                              "instructions": null,
                              "input": "textarea",
                              "type": "description",
                              "required": 0,
                              "default": null,
                              "priority": 0,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": []
                          },
                          {
                              "id": 5,
                              "key": "date_of_birth",
                              "label": "Date of birth",
                              "instructions": null,
                              "input": "date",
                              "type": "datetime",
                              "required": 0,
                              "default": null,
                              "priority": 3,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": "2020-06-01T07:04:10.921Z"
                          },
                          {
                              "id": 6,
                              "key": "missing_date",
                              "label": "Missing date",
                              "instructions": null,
                              "input": "date",
                              "type": "datetime",
                              "required": 0,
                              "default": null,
                              "priority": 4,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": "2020-06-02T07:04:10.921Z"
                          },
                          {
                              "id": 7,
                              "key": "last_location",
                              "label": "Last Location",
                              "instructions": null,
                              "input": "text",
                              "type": "varchar",
                              "required": 1,
                              "default": null,
                              "priority": 5,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": "Uruguay"
                              }
                          },
                          {
                              "id": 8,
                              "key": "last_location_point",
                              "label": "Last Location (point)",
                              "instructions": null,
                              "input": "location",
                              "type": "point",
                              "required": 0,
                              "default": null,
                              "priority": 5,
                              "options": null,
                              "cardinality": 0,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": {
                                      "lat": -22.03321543231222,
                                      "lon": 27.935730246117373
                                  }
                              }
                          },
                          {
                              "id": 9,
                              "key": "geometry_test",
                              "label": "Geometry test",
                              "instructions": null,
                              "input": "text",
                              "type": "geometry",
                              "required": 0,
                              "default": null,
                              "priority": 5,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": []
                          },
                          {
                              "id": 10,
                              "key": "missing_status",
                              "label": "Status",
                              "instructions": null,
                              "input": "select",
                              "type": "varchar",
                              "required": 0,
                              "default": null,
                              "priority": 5,
                              "options": [
                                  "information_sought",
                                  "is_note_author",
                                  "believed_alive",
                                  "believed_missing",
                                  "believed_dead"
                              ],
                              "cardinality": 0,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": "information_sought"
                              }
                          },
                          {
                              "id": 11,
                              "key": "links",
                              "label": "Links",
                              "instructions": null,
                              "input": "text",
                              "type": "varchar",
                              "required": 0,
                              "default": null,
                              "priority": 7,
                              "options": null,
                              "cardinality": 0,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": "https://google.com"
                              }
                          },
                          {
                              "id": 12,
                              "key": "second_point",
                              "label": "Second Point",
                              "instructions": null,
                              "input": "location",
                              "type": "point",
                              "required": 0,
                              "default": null,
                              "priority": 5,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": {
                                      "lat": -57.544489720135516,
                                      "lon": -169.81215934564818
                                  }
                              }
                          },
                          {
                              "id": 14,
                              "key": "media_test",
                              "label": "Media Test",
                              "instructions": null,
                              "input": "upload",
                              "type": "media",
                              "required": 0,
                              "default": null,
                              "priority": 7,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": null
                              }
                          },
                          {
                              "id": 15,
                              "key": "possible_actions",
                              "label": "Possible actions",
                              "instructions": null,
                              "input": "checkbox",
                              "type": "varchar",
                              "required": 0,
                              "default": null,
                              "priority": 5,
                              "options": [
                                  "ground_search",
                                  "medical_evacuation"
                              ],
                              "cardinality": 0,
                              "config": [],
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": [
                                      "medical_evacuation"
                                  ]
                              }
                          },
                          {
                              "id": 17,
                              "key": "title",
                              "label": "Title",
                              "instructions": null,
                              "input": "text",
                              "type": "title",
                              "required": 0,
                              "default": null,
                              "priority": 0,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": []
                          },
                          {
                              "id": 25,
                              "key": "markdown",
                              "label": "Test markdown",
                              "instructions": null,
                              "input": "text",
                              "type": "markdown",
                              "required": 0,
                              "default": null,
                              "priority": 0,
                              "options": null,
                              "cardinality": 1,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": {
                                  "value": "#markdowny"
                              }
                          },
                          {
                              "id": 26,
                              "key": "tags1",
                              "label": "Categories",
                              "instructions": null,
                              "input": "tags",
                              "type": "tags",
                              "required": 0,
                              "default": null,
                              "priority": 3,
                              "options": [],
                              "cardinality": 0,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 1,
                              "translations": [],
                              "value": []
                          }
                      ],
                      "translations": []
                  },
                  {
                      "id": 2,
                      "form_id": 1,
                      "label": "2nd step",
                      "priority": 2,
                      "required": 0,
                      "type": "task",
                      "description": null,
                      "show_when_published": 1,
                      "task_is_internal_only": 0,
                      "fields": [
                          {
                              "id": 13,
                              "key": "person_status",
                              "label": "Person Status",
                              "instructions": null,
                              "input": "select",
                              "type": "varchar",
                              "required": 0,
                              "default": null,
                              "priority": 5,
                              "options": [
                                  "information_sought",
                                  "is_note_author",
                                  "believed_alive",
                                  "believed_missing",
                                  "believed_dead"
                              ],
                              "cardinality": 0,
                              "config": null,
                              "response_private": 0,
                              "form_stage_id": 2,
                              "translations": [],
                              "value": {
                                  "value": "is_note_author"
                              }
                          }
                      ],
                      "translations": []
                  },
                  {
                      "id": 3,
                      "form_id": 1,
                      "label": "3rd step",
                      "priority": 3,
                      "required": 0,
                      "type": "task",
                      "description": null,
                      "show_when_published": 1,
                      "task_is_internal_only": 0,
                      "fields": [],
                      "translations": []
                  }
              ],
              "completed_stages": [
                  2,
                  3
              ],
              "published_to": [],
              "post_date": "2020-06-24T07:04:07.897Z",
              "enabled_languages": {},
              "allowed_privileges": [
                  "read",
                  "create",
                  "update",
                  "delete",
                  "search",
                  "change_status",
                  "read_full"
              ],
              "content": "A description",
              "base_language": "",
              "type": "report",
              "form_id": 1
          }
        """
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the response has a "result.title" property
    And the "result.title" property equals "A title"
    And the "result.post_content.0.fields.9.type" property equals "point"
    And the "result.post_content.0.fields.9.value.value.lon" property equals "27.935730246117"
    And the "result.post_content.0.fields.15.key" property equals "links"
    And the "result.post_content.0.fields.15.type" property equals "varchar"
    And the "result.post_content.0.fields.15.value.value" property equals "https://google.com"
    And the type of the "result.completed_stages.0.form_stage_id" property is "int"
    And the "result.completed_stages.0.form_stage_id" property equals "2"
    And the "result.completed_stages.1.form_stage_id" property equals "3"
#    And the "result.post_date" property equals "2016-10-14T23:18:27+00:00"
    Then the guzzle status code should be 201
