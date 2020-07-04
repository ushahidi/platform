@post @rolesEnabled
Feature: Testing the Posts API
  @create @rolesEnabled
  Scenario: Creating a new Post
    Given that I want to make a new "Post"
    And that the oauth token is "testadminuser"
    And that the api_url is "api/v5"
    And that the request "data" is:
      """
        {
            "title": "A title",
            "description": "",
            "locale": "en_US",
            "post_content": [
                {
                    "id": 1,
                    "type": "post",
                    "fields": [
                        {
                            "id": 1,
                            "type": "varchar",
                            "form_stage_id": 1,
                            "translations": [],
                            "value": {
                                "value": "MY VARCHAR"
                            }
                        },
                        {
                            "id": 2,
                            "type": "point",
                            "form_stage_id": 1,
                            "value": {
                                "value": {
                                    "lat": -18.892817463050697,
                                    "lon": 22.840418464728486
                                }
                            }
                        },
                        {
                            "id": 3,
                            "type": "varchar",
                            "form_stage_id": 1,
                            "translations": [],
                            "value": {
                                "value": "A full name"
                            }
                        },
                        {
                            "id": 5,
                            "type": "datetime",
                            "form_stage_id": 1,
                            "value": "2020-06-01T07:04:10.921Z"
                        },
                        {
                            "id": 6,
                            "type": "datetime",
                            "form_stage_id": 1,
                            "value": "2020-06-02T07:04:10.921Z"
                        },
                        {
                            "id": 7,
                            "type": "varchar",
                            "form_stage_id": 1,
                            "value": {
                                "value": "Uruguay"
                            }
                        },
                        {
                            "id": 8,
                            "type": "point",
                            "form_stage_id": 1,
                            "value": {
                                "value": {
                                    "lat": -22.03321543231222,
                                    "lon": 27.935730246117373
                                }
                            }
                        },
                        {
                            "id": 10,
                            "type": "varchar",
                            "form_stage_id": 1,
                            "value": {
                                "value": "information_sought"
                            }
                        },
                        {
                            "id": 11,
                            "type": "varchar",
                            "form_stage_id": 1,
                            "value": {
                                "value": "https://google.com"
                            }
                        },
                        {
                            "id": 12,
                            "type": "point",
                            "form_stage_id": 1,
                            "value": {
                                "value": {
                                    "lat": -57.544489720135516,
                                    "lon": -169.81215934564818
                                }
                            }
                        },
                        {
                            "id": 14,
                            "type": "media",
                            "form_stage_id": 1,
                            "value": {
                                "value": null
                            }
                        },
                        {
                            "id": 15,
                            "type": "varchar",
                            "form_stage_id": 1,
                            "value": {
                                "value": [
                                    "medical_evacuation"
                                ]
                            }
                        },
                        {
                            "id": 25,
                            "type": "markdown",
                            "form_stage_id": 1,
                            "value": {
                                "value": "#markdowny"
                            }
                        },
                        {
                            "id": 26,
                            "type": "tags",
                            "form_stage_id": 1,
                            "value": {
                                "value": [1,2]
                            }

                        }
                    ]
                },
                {
                    "id": 2,
                    "form_id": 1,
                    "type": "task",
                    "fields": [
                        {
                            "id": 13,
                            "type": "varchar",
                            "form_stage_id": 2,
                            "value": {
                                "value": "is_note_author"
                            }
                        }
                    ]
                }
            ],
            "completed_stages": [
                2,
                3
            ],
            "published_to": [],
            "post_date": "2020-06-24T07:04:07.897Z",
            "enabled_languages": {},
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
    And the "result.content" property equals "A description"
    And the "result.user_id" property equals "2"
    And the "result.categories" property count is "2"
    And the "result.completed_stages" property count is "2"
    And the type of the "result.completed_stages.0.form_stage_id" property is "int"
    And the "result.completed_stages.0.form_stage_id" property equals "2"
    And the "result.completed_stages.1.form_stage_id" property equals "3"
    And the "result.post_content.0.fields.1.type" property equals "markdown"
    And the "result.post_content.0.fields.1.value.value" property equals "#markdowny"
    And the "result.post_content.0.fields.3.type" property equals "varchar"
    And the "result.post_content.0.fields.3.value.value" property equals "MY VARCHAR"
    And the "result.post_content.0.fields.4.type" property equals "point"
    And the "result.post_content.0.fields.4.value.value.lon" property equals "22.840418464728"
    And the "result.post_content.0.fields.4.value.value.lat" property equals "-18.892817463051"
    And the "result.post_content.0.fields.6.type" property equals "tags"
    And the "result.post_content.0.fields.6.value" property count is "2"
    And the "result.post_content.0.fields.6.value.0.tag" property equals "Test tag"
    And the "result.post_content.0.fields.6.value.1.tag" property equals "Duplicate"
    And the "result.post_content.0.fields.9.type" property equals "point"
    And the "result.post_content.0.fields.9.value.value.lon" property equals "27.935730246117"
    And the "result.post_content.0.fields.15.key" property equals "links"
    And the "result.post_content.0.fields.15.type" property equals "varchar"
    And the "result.post_content.0.fields.15.value.value" property equals "https://google.com"
#    And the "result.post_date" property equals "2016-10-14T23:18:27+00:00"
    Then the guzzle status code should be 201
  @create @rolesEnabled
  Scenario: Updating a Post
    Given that I want to update a "Post"
    And that the oauth token is "testmanager"
    And that the api_url is "api/v5"
    And that the request "data" is:
        """
          {
              "id": 105,
              "form_id": 1,
              "user_id": 3,
              "type": "report",
              "title": "Original post",
              "slug": null,
              "content": "Some description",
              "author_email": null,
              "author_realname": null,
              "status": "published",
              "published_to": [],
              "locale": "en_us",
              "created": "2013-07-05 00:00:00",
              "updated": null,
              "post_date": "2013-07-04 23:36:05",
              "base_language": "",
              "categories": [],
              "completed_stages": [],
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
                          },
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
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
                              "options": [
                                  "1",
                                  "2",
                                  "3",
                                  "4",
                                  "5",
                                  "6",
                                  "7"
                              ],
                              "cardinality": 0,
                              "config": null,
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": []
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": {
                                  "id": 23,
                                  "post_id": 105,
                                  "value": "Atlantis",
                                  "form_attribute_id": 7,
                                  "created": null,
                                  "translations": []
                              }
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": {
                                  "value": "information_sought"
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": {
                                  "value": [
                                      "medical_evacuation"
                                  ]
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": null
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": {
                                  "value": "https://google2.com"
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
                              "form_stage_id": 1,
                              "response_private": 0,
                              "description": null,
                              "translations": [],
                              "value": {
                                  "value": null
                              }
                          }
                      ],
                      "translations": []
                  }
              ],
              "translations": {},
              "enabled_languages": {
                  "default": "",
                  "available": []
              }
          }
        """
    And that its "id" is "105"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.id" property equals "105"
#    And the response has a "result.title" property
#    And the "result.title" property equals "A title"
#    And the "result.post_content.0.fields.9.type" property equals "point"
#    And the "result.post_content.0.fields.9.value.value.lon" property equals "27.935730246117"
#    And the "result.post_content.0.fields.15.key" property equals "links"
#    And the "result.post_content.0.fields.15.type" property equals "varchar"
#    And the "result.post_content.0.fields.15.value.value" property equals "https://google.com"
#    And the type of the "result.completed_stages.0.form_stage_id" property is "int"
#    And the "result.completed_stages.0.form_stage_id" property equals "2"
#    And the "result.completed_stages.1.form_stage_id" property equals "3"
#    And the "result.post_date" property equals "2016-10-14T23:18:27+00:00"
    Then the guzzle status code should be 200
