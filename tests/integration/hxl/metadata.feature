@hxl @hxlEnabled
Feature: Testing the HXL Metadata API
  Scenario: Create a metadata object for an existing export job
    Given that I want to make a new "HXL Metadata"
    And that the oauth token is "testadminuser"
    And that the request "data" is:
          """
          {
              "license_id": 1,
              "organisation": "ushahidi",
              "dataset_title": "ushahidi-dataset",
              "source": "other",
              "maintainer": "ushahidi-maintainer",
              "private": true,
              "export_job_id":1
          }
          """
    When I request "/hxl/metadata"
    Then the response is JSON
    And the response has a "id" property
    And the type of the "id" property is "numeric"
    Then the guzzle status code should be 200
  Scenario: Create a metadata fails if I'm not the owner of the metadata object
    Given that I want to make a new "HXL Metadata"
    And that the oauth token is "testadminuser"
    And that the request "data" is:
          """
          {
              "license_id": 1,
              "organisation": "ushahidi",
              "dataset_title": "ushahidi-dataset",
              "source": "other",
              "maintainer": "ushahidi-maintainer",
              "private": true,
              "export_job_id":1,
              "user_id": 10
          }
          """
    When I request "/hxl/metadata"
    Then the response is JSON
    And the response has a "errors.0.message" property
    And the "errors.0.message" property equals "User 2 is not allowed to create resource hxl_meta_data #0"
    Then the guzzle status code should be 403
  Scenario: Create a metadata object fails if the license does not exists
    Given that I want to make a new "HXL Metadata"
    And that the oauth token is "testadminuser"
    And that the request "data" is:
          """
          {
              "license_id": 999,
              "organisation": "ushahidi",
              "dataset_title": "ushahidi-dataset",
              "source": "other",
              "maintainer": "ushahidi-maintainer",
              "private": true,
              "export_job_id":1
          }
          """
    When I request "/hxl/metadata"
    Then the response is JSON
    And the response has a "errors.0.message" property
    And the response has a "errors.1.source" property
    And the "errors.1.source.pointer" property equals "/license_id"
    Then the guzzle status code should be 422
  Scenario: Create a metadata object fails if the exportjob does not exists
    Given that I want to make a new "HXL Metadata"
    And that the oauth token is "testadminuser"
    And that the request "data" is:
          """
          {
              "license_id": 1,
              "organisation": "ushahidi",
              "dataset_title": "ushahidi-dataset",
              "source": "other",
              "maintainer": "ushahidi-maintainer",
              "private": true,
              "export_job_id":99
          }
          """
    When I request "/hxl/metadata"
    Then the response is JSON
    And the response has a "errors.0.message" property
    And the response has a "errors.1.source" property
    And the "errors.1.source.pointer" property equals "/export_job_id"
    Then the guzzle status code should be 422