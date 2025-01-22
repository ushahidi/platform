@rolesEnabled
Feature: Testing the Surveys API
  Scenario: Updating a Survey
    Given that I want to update a "Survey"
    And that the oauth token is "testmanager"
    And that the api_url is "api/v5"
    And that the request "data" is:
    """
                {
                    "id": 1,
                    "name": "Test Form has been updated name",
                    "parent_id": null,
                    "description": "Testing form is updated desc",
                    "type": "report",
                    "disabled": 0,
                    "require_approval": 0,
                    "everyone_can_create": 0,
                    "color": "#A51A1A",
                    "hide_author": 0,
                    "hide_time": 0,
                    "hide_location": 0,
                    "targeted_survey": 0,
                    "base_language": "en",
                    "translations": {
                        "es": {
                            "name": "ES Test Form has been updated name",
                            "description": "ES Testing form is updated desc"
                        }
                    },
                    "tasks": [
                        {
                            "id": 1,
                            "form_id": 1,
                            "label": "Main task 1 updated",
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Test varchar"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Test point"
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Full Name"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Description"
                                        }
                                    }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Date of birth"
                                        }
                                    }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Missing date"
                                        }
                                    }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Last Location"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Last Location (point)"
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Geometry test"
                                        }
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
                                    "response_private": 0,
                                    "form_stage_id": 1,
                                    "translations": {
                                        "es": {
                                            "label": "ES Status"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Links"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Second Point"
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Media Test"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Possible actions"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Title"
                                        }
                                    }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Test markdown"
                                        }
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
                                    "response_private": 0,
                                    "form_stage_id": 1,
                                    "translations": {
                                        "es": {
                                            "label": "ES Categories",
                                            "options": [
                                                "ES 1",
                                                "ES 2",
                                                "ES 3",
                                                "ES 4",
                                                "ES 5",
                                                "ES 6",
                                                "ES 7"
                                            ]
                                        }
                                    }
                                }
                            ],
                            "translations": {
                                "es": {
                                    "label": "ES Main task 1"
                                }
                            }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Person Status"
                                        }
                                    }
                                }
                            ],
                            "translations": {
                                "es": {
                                    "label": "ES 2nd step"
                                }
                            }
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
                    ]
                }
                """
    And that its "id" is "1"
    When I request "/surveys"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.id" property equals "1"
    And the response has a "result.name" property
    And the "result.name" property equals "Test Form has been updated name"
    And the "result.disabled" property is false
    And the "result.require_approval" property is false
    And the "result.everyone_can_create" property is false
    And the "result.color" property equals "#A51A1A"
    And the "result.translations.es.name" property equals "ES Test Form has been updated name"
    And the "result.tasks" property count is "3"
    And the "result.tasks.0.fields" property count is "17"
    And the "result.tasks.0.label" property equals "Main task 1 updated"
    And the "result.tasks.0.translations.es.label" property equals "ES Main task 1"
    And the "result.tasks.0.fields.0.label" property equals "Test varchar"
    And the "result.tasks.0.fields.0.translations.es.label" property equals "ES Test varchar"
    And the "result.tasks.1.fields" property count is "1"
    And the "result.tasks.1.translations.es.label" property equals "ES 2nd step"
    And the "result.tasks.1.fields.0.translations.es.label" property equals "ES Person Status"
    And the "result.tasks.2.fields" property count is "0"
    Then the guzzle status code should be 200
  Scenario: Update survey including removing one task and a field from another task
    Given that I want to update a "Survey"
    And that the oauth token is "testmanager"
    And that the api_url is "api/v5"
    And that the request "data" is:
    """
                {
                    "id": 1,
                    "name": "UPDATED Form has been updated name",
                    "parent_id": null,
                    "description": "UPDATED Testing form is updated desc",
                    "type": "report",
                    "disabled": 0,
                    "require_approval": 0,
                    "everyone_can_create": 0,
                    "color": "#A51A1B",
                    "hide_author": 0,
                    "hide_time": 0,
                    "hide_location": 0,
                    "targeted_survey": 0,
                    "base_language": "en",
                    "translations": {
                        "es": {
                            "name": "UPDATED ES Test Form has been updated name",
                            "description": "UPDATED ES Testing form is updated desc"
                        }
                    },
                    "tasks": [
                        {
                            "id": 1,
                            "form_id": 1,
                            "label": "Main task 1 updated",
                            "priority": 1,
                            "required": 0,
                            "type": "post",
                            "description": null,
                            "show_when_published": 1,
                            "task_is_internal_only": 0,
                            "fields": [
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Test point"
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Full Name"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Description"
                                        }
                                    }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Date of birth"
                                        }
                                    }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Missing date"
                                        }
                                    }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Last Location"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Last Location (point)"
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Geometry test"
                                        }
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
                                    "response_private": 0,
                                    "form_stage_id": 1,
                                    "translations": {
                                        "es": {
                                            "label": "ES Status"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Links"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Second Point"
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Media Test"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Possible actions"
                                        }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Title"
                                        }
                                    }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Test markdown"
                                        }
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
                                    "response_private": 0,
                                    "form_stage_id": 1,
                                    "translations": {
                                        "es": {
                                            "label": "ES Categories",
                                            "options": [
                                                "ES 1",
                                                "ES 2",
                                                "ES 3",
                                                "ES 4",
                                                "ES 5",
                                                "ES 6",
                                                "ES 7"
                                            ]
                                        }
                                    }
                                }
                            ],
                            "translations": {
                                "es": {
                                    "label": "ES Main task 1"
                                }
                            }
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
                                    "translations": {
                                        "es": {
                                            "label": "ES Person Status"
                                        }
                                    }
                                }
                            ],
                            "translations": {
                                "es": {
                                    "label": "ES 2nd step"
                                }
                            }
                        }
                    ]
                }
                """
    And that its "id" is "1"
    When I request "/surveys"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.id" property equals "1"
    And the response has a "result.name" property
    And the "result.name" property equals "UPDATED Form has been updated name"
    And the "result.description" property equals "UPDATED Testing form is updated desc"
    And the "result.disabled" property is false
    And the "result.require_approval" property is false
    And the "result.everyone_can_create" property is false
    And the "result.color" property equals "#A51A1B"
    And the "result.translations.es.name" property equals "UPDATED ES Test Form has been updated name"
    And the "result.tasks" property count is "2"
    And the "result.tasks.0.fields" property count is "16"
    And the "result.tasks.0.label" property equals "Main task 1 updated"
    And the "result.tasks.0.translations.es.label" property equals "ES Main task 1"
    And the "result.tasks.0.fields.0.label" property equals "Test point"
    And the "result.tasks.0.fields.0.translations.es.label" property equals "ES Test point"
    And the "result.tasks.1.fields" property count is "1"
    And the "result.tasks.1.translations.es.label" property equals "ES 2nd step"
    And the "result.tasks.1.fields.0.translations.es.label" property equals "ES Person Status"
    Then the guzzle status code should be 200
