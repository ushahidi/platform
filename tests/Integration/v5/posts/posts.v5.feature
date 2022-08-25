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
            "content": "A description",
            "locale": "en_US",
            "post_content": [
                {
                    "id": 1,
                    "type": "post",
                    "fields": [
                        {
                            "id": 1,
                            "type": "varchar",
                            "translations": [],
                            "value": {
                                "value": "MY VARCHAR"
                            }
                        },
                        {
                            "id": 2,
                            "type": "point",
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
                            "translations": [],
                            "value": {
                                "value": "A full name"
                            }
                        },
                        {
                            "id": 5,
                            "type": "datetime",
                            "value": {
                                "value": "2020-06-01T07:04:10.921Z",
                                "value_meta": {
                                    "from_tz": "Africa/Nairobi",
                                    "from_dst": false
                                }
                            }
                        },
                        {
                            "id": 6,
                            "type": "datetime",
                            "value": {
                                "value": "2020-06-02"
                            }
                        },
                        {
                            "id": 7,
                            "type": "varchar",
                            "value": {
                                "value": "Uruguay"
                            }
                        },
                        {
                            "id": 8,
                            "type": "point",
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
                            "value": {
                                "value": "information_sought"
                            }
                        },
                        {
                            "id": 11,
                            "type": "varchar",
                            "value": {
                                "value": "https://google.com"
                            }
                        },
                        {
                            "id": 12,
                            "type": "point",
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
                            "value": {
                                "value": null
                            }
                        },
                        {
                            "id": 15,
                            "type": "varchar",
                            "value": {
                                "value": [
                                    "medical_evacuation"
                                ]
                            }
                        },
                        {
                            "id": 25,
                            "type": "markdown",
                            "value": {
                                "value": "#markdowny"
                            }
                        },
                        {
                            "id": 26,
                            "type": "tags",
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
    And the "result.status" property equals "published"
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
    #And the "result.post_date" property equals "2016-10-14T23:18:27+00:00"
    Then the guzzle status code should be 201
  @create @rolesEnabled
  Scenario: Updating a Post
    Given that I want to update a "Post"
    And that the oauth token is "testadminuser"
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
                      "fields": [
                          {
                              "id": 4,
                              "type": "description",
                              "value": null
                          },
                          {
                              "id": 25,
                              "type": "markdown",
                              "value": {
                                "value": "A markdown content",
                                "translations": {
                                  "es": {
                                    "value": "A markdown content ES"
                                  }
                                }
                              }
                          },
                          {
                              "id": 17,
                              "type": "title",
                              "value": null
                          },
                          {
                              "id": 1,
                              "type": "varchar",
                              "value": {
                                "value": "A varchar content",
                                "translations": {
                                  "es": {
                                    "value": "A varchar content ES"
                                  }
                                }
                              }
                          },
                          {
                              "id": 2,
                              "type": "point",
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
                              "value": {
                                "value": "Full name",
                                "translations": {
                                  "es": {
                                    "value": "Full name ES"
                                  }
                                }
                              }
                          },
                          {
                              "id": 26,
                              "type": "tags",
                              "value": {
                                "value": [1]
                              }
                          },
                          {
                              "id": 5,
                              "type": "datetime",
                              "value": {
                                "value": "2019-02-02"
                              }
                          },
                          {
                              "id": 6,
                              "type": "datetime",
                              "value": {
                                "value": "2019-02-03"
                              }
                          },
                          {
                              "id": 8,
                              "type": "point",
                              "value": null
                          },
                          {
                              "id": 7,
                              "type": "varchar",
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
                              "type": "varchar",
                              "value": {
                                  "value": "information_sought"
                              }
                          },
                          {
                              "id": 12,
                              "type": "point",
                              "value": null
                          },
                          {
                              "id": 15,
                              "type": "varchar",
                              "value": {
                                  "value": [
                                      "medical_evacuation"
                                  ]
                              }
                          },
                          {
                              "id": 9,
                              "type": "geometry",
                              "value": null
                          },
                          {
                              "id": 11,
                              "type": "varchar",
                              "value": {
                                  "value": "https://google-modified.com",
                                  "translations": {
                                    "es": {
                                      "value": "https://google-modified-es.com"
                                    }
                                  }
                              }
                          },
                          {
                              "id": 14,
                              "type": "media",
                              "value": {
                                  "value": null
                              }
                          }
                      ],
                      "translations": []
                  }
              ],
              "translations": {
                "es": {
                  "title": "Original post ES",
                  "content": "Some description ES"
                }
              },
              "enabled_languages": {
                  "default": "en",
                  "available": ["es"]
              }
          }
        """
    And that its "id" is "105"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.id" property equals "105"
    And the response has a "result.title" property
    And the "result.title" property equals "Original post"
    And the "result.translations.es.title" property equals "Original post ES"
    And the "result.content" property equals "Some description"
    And the "result.translations.es.content" property equals "Some description ES"
    And the "result.post_content.0.id" property equals "1"
    And the "result.post_content.0.fields" property count is "17"
    And the "result.post_content.0.fields.1.type" property equals "markdown"
    And the "result.post_content.0.fields.1.value.value" property equals "A markdown content"
    And the "result.post_content.0.fields.1.value.translations.es.value" property equals "A markdown content ES"
    And the "result.post_content.0.fields.3.type" property equals "varchar"
    And the "result.post_content.0.fields.3.value.value" property equals "A varchar content"
    And the "result.post_content.0.fields.3.value.translations.es.value" property equals "A varchar content ES"
    And the "result.post_content.0.fields.4.type" property equals "point"
    And the "result.post_content.0.fields.4.value.value.lon" property equals "22.840418464728"
    And the "result.post_content.0.fields.4.value.value.lat" property equals "-18.892817463051"
    And the "result.post_content.0.fields.5.type" property equals "varchar"
    And the "result.post_content.0.fields.5.value.value" property equals "Full name"
    And the "result.post_content.0.fields.5.value.translations.es.value" property equals "Full name ES"
    And the "result.post_content.0.fields.6.type" property equals "tags"
    And the "result.post_content.0.fields.6.value.0.id" property equals "1"
    And the "result.post_content.0.fields.6.value.0.tag" property equals "Test tag"
    And the "result.post_content.0.fields.7.type" property equals "datetime"
    And the "result.post_content.0.fields.7.value.value" property equals "2019-02-02"
    And the "result.post_content.0.fields.10.type" property equals "varchar"
    And the "result.post_content.0.fields.10.key" property equals "last_location"
    And the "result.post_content.0.fields.10.value.value" property equals "Atlantis"
    And the "result.post_content.0.fields.11.type" property equals "varchar"
    And the "result.post_content.0.fields.11.key" property equals "missing_status"
    And the "result.post_content.0.fields.11.value.value" property equals "information_sought"
    And the "result.post_content.0.fields.13.type" property equals "varchar"
    And the "result.post_content.0.fields.13.key" property equals "possible_actions"
    And the "result.post_content.0.fields.13.value.value.0" property equals "medical_evacuation"
    And the "result.post_content.0.fields.15.type" property equals "varchar"
    And the "result.post_content.0.fields.15.value.value" property equals "https://google-modified.com"
    And the "result.post_content.0.fields.15.value.translations.es.value" property equals "https://google-modified-es.com"
    Then the guzzle status code should be 200
  @create @rolesEnabled
  Scenario: Updating a Post to remove markdown field (id:25) and tags (id: 26)
    Given that I want to update a "Post"
    And that the oauth token is "testadminuser"
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
                      "fields": [
                          {
                              "id": 4,
                              "type": "description",
                              "value": null
                          },
                          {
                              "id": 25,
                              "type": "markdown",
                              "value": {
                                "value": "",
                                "translations": {
                                  "es": {}
                                }
                              }
                          },
                          {
                              "id": 17,
                              "type": "title",
                              "value": null
                          },
                          {
                              "id": 1,
                              "type": "varchar",
                              "value": {
                                "value": "A varchar content",
                                "translations": {
                                  "es": {
                                    "value": "A varchar content ES"
                                  }
                                }
                              }
                          },
                          {
                              "id": 2,
                              "type": "point",
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
                              "value": {
                                "value": "Full name",
                                "translations": {
                                  "es": {
                                    "value": "Full name ES"
                                  }
                                }
                              }
                          },
                          {
                              "id": 26,
                              "type": "tags",
                              "value": {
                                "value": []
                              }
                          },
                          {
                              "id": 5,
                              "type": "datetime",
                              "value": {
                                "value": "2019-02-02"
                              }
                          },
                          {
                              "id": 6,
                              "type": "datetime",
                              "value": {
                                "value": "2019-02-03"
                              }
                          },
                          {
                              "id": 8,
                              "type": "point",
                              "value": null
                          },
                          {
                              "id": 7,
                              "type": "varchar",
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
                              "type": "varchar",
                              "value": {
                                  "value": "information_sought"
                              }
                          },
                          {
                              "id": 12,
                              "type": "point",
                              "value": null
                          },
                          {
                              "id": 15,
                              "type": "varchar",
                              "value": {
                                  "value": [
                                      "medical_evacuation"
                                  ]
                              }
                          },
                          {
                              "id": 9,
                              "type": "geometry",
                              "value": null
                          },
                          {
                              "id": 11,
                              "type": "varchar",
                              "value": {
                                  "value": "https://google-modified.com",
                                  "translations": {
                                    "es": {
                                      "value": "https://google-modified-es.com"
                                    }
                                  }
                              }
                          },
                          {
                              "id": 14,
                              "type": "media",
                              "value": {
                                  "value": null
                              }
                          }
                      ],
                      "translations": []
                  }
              ],
              "translations": {
                "es": {
                  "title": "Original post ES",
                  "content": "Some description ES"
                }
              },
              "enabled_languages": {
                  "default": "en",
                  "available": ["es"]
              }
          }
        """
    And that its "id" is "105"
    When I request "/posts"
    Then the response is JSON
    And the response has a "result.id" property
    And the type of the "result.id" property is "numeric"
    And the "result.id" property equals "105"
    And the response has a "result.title" property
    And the "result.title" property equals "Original post"
    And the "result.translations.es.title" property equals "Original post ES"
    And the "result.content" property equals "Some description"
    And the "result.translations.es.content" property equals "Some description ES"
    And the "result.post_content.0.id" property equals "1"
    And the "result.post_content.0.fields" property count is "17"
    And the "result.post_content.0.fields.1.type" property equals "markdown"
    And the "result.post_content.0.fields.1.value.value" property is empty
    And the "result.post_content.0.fields.1.value.translations.es.value" property is empty
    And the "result.post_content.0.fields.6.type" property equals "tags"
    And the "result.post_content.0.fields.6.value.value" property is empty
    Then the guzzle status code should be 200
