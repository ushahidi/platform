@acl
Feature: Testing translations
    @rolesEnabled
        Scenario: User with Manage Settings permission can create a hydrated form
        Given that I want to make a new "Survey"
        And that the oauth token is "testmanager"
        And that the api_url is "api/v4"
        And that the request "data" is:
        """
            {
                "enabled_languages": {
                    "default": "en-EN",
                    "available": ["es"]
                },
                "color": null,
                "require_approval": true,
                "everyone_can_create": false,
                "targeted_survey": false,
                "translations": {
                    "es": {
                            "name": "nombre"
                    }
                },
                "tasks": [
                    {
                        "translations": {
                            "es": {
                                "label": "Reporte",
                                "description": "Una descripcion"
                            }
                        },
                        "label": "Post",
                        "priority": 0,
                        "required": false,
                        "type": "post",
                        "show_when_published": true,
                        "task_is_internal_only": false,
                        "fields": [
                            {
                                "cardinality": 0,
                                "input": "text",
                                "label": "Title",
                                "priority": 1,
                                "required": true,
                                "type": "title",
                                "options": [],
                                "config": {},
                                "translations": {
                                    "es": {
                                        "label": "Titulo",
                                        "instructions": "Instrucciones",
                                        "default": "Un valor por defecto",
                                        "options": ["Una opcion", "otra opcion"]
                                    }
                                }
                            },
                            {
                                "cardinality": 0,
                                "input": "text",
                                "label": "Description",
                                "priority": 2,
                                "required": true,
                                "type": "description",
                                "options": [],
                                "config": {},
                                "translations": {
                                    "es": {
                                        "label": "Descripcion",
                                        "instructions": "Instrucciones de la desc",
                                        "default": "Un valor por defecto para desc",
                                        "options": ["Una opcion", "otra opcion desc"]
                                    }
                                }
                            }
                        ],
                        "is_public": true
                    }
                ],
                "name": "new"
            }
        """
        When I request "/surveys"
        Then the response is JSON
        And the response has a "result" property
        And the response has a "result.id" property
        And the type of the "result.id" property is "numeric"
        And the response has a "result.tasks" property
        And the type of the "result.tasks" property is "array"
        And the response has a "result.tasks.0.fields" property
        And the "result.tasks.0.fields" property count is "2"
        And the "result.translations.es.0.name" property equals "label"
        And the "result.tasks.0.translations.es.label" property equals "Reporte"
        And the "result.name" property equals "new"
        Then the guzzle status code should be 200
