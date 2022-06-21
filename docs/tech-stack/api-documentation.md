# API Documentation

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/oauth/token" method="post" summary="Get an authorization code for the client (without a user login)" %}
{% swagger-description %}
This endpoint allows you to get an authorization token for the client without a user login. It allows you to execute the same actions as any non-logged in user.
{% endswagger-description %}

{% swagger-parameter in="body" name="grant_type" type="string" %}
Expected value for this type: "client_credentials"
{% endswagger-parameter %}

{% swagger-parameter in="body" name="client_secret" type="string" %}
The client secret you added for your deployment in the database. Default value: "35e7f0bca957836d05ca0492211b0ac707671261"
{% endswagger-parameter %}

{% swagger-parameter in="body" name="client_id" type="string" %}
The client_id you created for your deployment. Default value: "ushahidiui"
{% endswagger-parameter %}

{% swagger-parameter in="body" name="scope" type="string" %}
All allowed scopes for this type: "posts country_codes media forms api tags savedsearches sets users stats layers config messages notifications webhooks contacts permissions csv"
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/oauth/token" method="post" summary="Get an authorization code for a user" %}
{% swagger-description %}
This endpoint allows you to get an authentication token. All fields are required.
{% endswagger-description %}

{% swagger-parameter in="body" name="scope" type="string" %}
Default value: *
{% endswagger-parameter %}

{% swagger-parameter in="body" name="client_secret" type="string" %}
Your client secret. Default value: 35e7f0bca957836d05ca0492211b0ac707671261
{% endswagger-parameter %}

{% swagger-parameter in="body" name="client_id" type="string" %}
Your client ID. Default value: ushahidiui
{% endswagger-parameter %}

{% swagger-parameter in="body" name="grant_type" type="string" %}
Fixed. Send value: password
{% endswagger-parameter %}

{% swagger-parameter in="body" name="password" type="string" %}
Your Ushahidi platform password
{% endswagger-parameter %}

{% swagger-parameter in="body" name="username" type="string" %}
Your Ushahidi platform username
{% endswagger-parameter %}

{% swagger-response status="200" description="Auth token created" %}
```javascript
{
    "token_type": "Bearer",
    "expires_in": "86400",
    "access_token": "averylongstring",
    "refresh_token": "anotherverylongstring"
}
```
{% endswagger-response %}

{% swagger-response status="400" description="Incorrect credentials" %}
```javascript
{
    "error": "invalid_request",
    "error_description": "The user credentials were incorrect."
}
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/passwordreset" method="post" summary="Request a password reset token to be sent via email" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="body" name="email" type="string" %}
example: youremail@example.com
{% endswagger-parameter %}

{% swagger-response status="204" description="" %}
```
This response is sent when the password reset email is going to be sent (as long as the email exists)
```
{% endswagger-response %}

{% swagger-response status="400" description="Incorrect parameter name in the body (emails, instead of email)" %}
```javascript
{
    "errors": [
        {
            "status": 400,
            "message": "Bad request: Payload parameter email has not been declared, defined parameters are: emails"
        }
    ]
}
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/passwordreset/confirm" method="post" summary="Reset your password with the reset token" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="body" name="password" type="string" %}
The new password
{% endswagger-parameter %}

{% swagger-parameter in="body" name="token" type="string" %}
The reset token sent by email
{% endswagger-parameter %}

{% swagger-response status="204" description="" %}
```
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/register" method="post" summary="Register a new user" %}
{% swagger-description %}
Register your Ushahidi platform users with this endpoint.
{% endswagger-description %}

{% swagger-parameter in="path" name="" type="string" %}

{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/collections" method="get" summary="Get Collections" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "count": 2,
    "results": [
        {
            "id": 15,
            "url": null,
            "user": {
                "id": 1,
                "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/users\/86"
            },
            "name": "Testing",
            "description": "tests",
            "view": "map",
            "view_options": null,
            "role": null,
            "featured": true,
            "created": "2018-11-27T15:26:35+00:00",
            "updated": null,
            "allowed_privileges": [
                "read",
                "search"
            ]
        },
        {
            "id": 14,
            "url": null,
            "user": {
                "id": 1,
                "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/users\/86"
            },
            "name": "Testing",
            "description": "tests",
            "view": "data",
            "view_options": null,
            "role": null,
            "featured": true,
            "created": "2018-11-27T15:26:35+00:00",
            "updated": null,
            "allowed_privileges": [
                "read",
                "search"
            ]
        }
    ],
    "limit": null,
    "offset": 0,
    "order": "DESC",
    "orderby": "created",
    "curr": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/collections?orderby=created&order=DESC&offset=0",
    "next": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/collections?orderby=created&order=DESC&offset=0",
    "prev": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/collections?orderby=created&order=DESC&offset=0",
    "total_count": 2
}
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/posts?order=desc&orderby=created&set=15" method="get" summary="Get Posts from a collection" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="query" name="set" type="string" %}
:collectionId (required to get posts from a collection)
{% endswagger-parameter %}

{% swagger-parameter in="query" name="orderby" type="string" %}
Options: desc, asc
{% endswagger-parameter %}

{% swagger-parameter in="query" name="limit" type="string" %}
:number
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "count": 2,
    "results": [
        {
            "id": 18100,
            "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts\/18100",
            "parent_id": null,
            "form": {
                "id": 2,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/forms\/2"
            },
            "user_id": null,
            "message": null,
            "color": null,
            "type": "report",
            "title": "401 edits",
            "slug": "401-5bfd5fce5eba2",
            "content": "Tests",
            "status": "published",
            "created": "2018-11-27T15:16:33+00:00",
            "updated": "2018-11-27T15:47:13+00:00",
            "locale": "en_us",
            "values": {
                "fc81397d-b73d-43a6-b1da-7614534563be": [
                    "2018-11-22 05:31:00"
                ],
                "cc0cb71a-c0af-4e0a-94ac-be6b21f4b796": [
                    "2018-11-27 15:46:50"
                ],
                "9b65d16f-023d-4c1c-9284-8a3d57c8ae0a": [
                    {
                        "lon": 9.563599,
                        "lat": 7.710992
                    }
                ]
            },
            "post_date": "2018-11-27T15:16:33+00:00",
            "tags": [],
            "published_to": [],
            "completed_stages": [],
            "sets": [
                "14"
            ],
            "lock": null,
            "source": null,
            "contact": null,
            "data_source_message_id": null,
            "allowed_privileges": [
                "read",
                "create",
                "search"
            ]
        },
        {
            "id": 8328,
            "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts\/8328",
            "parent_id": null,
            "form": {
                "id": 1,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1"
            },
            "user_id": null,
            "message": {
                "id": 23462,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/messages\/23462"
            },
            "color": null,
            "type": "report",
            "title": "Test 2.",
            "slug": "-58fdc11e54991",
            "content": "Some content",
            "status": "published",
            "created": "2017-04-24T09:10:54+00:00",
            "updated": "2017-04-24T13:10:19+00:00",
            "locale": "en_us",
            "values": [],
            "post_date": "2017-04-24T09:10:54+00:00",
            "tags": [],
            "published_to": [],
            "completed_stages": [
                1
            ],
            "sets": [
                "1",
                "3"
            ],
            "lock": null,
            "source": "sms",
            "contact": {
                "id": 2693,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/contact\/2693"
            },
            "data_source_message_id": null,
            "allowed_privileges": [
                "read",
                "create",
                "search"
            ]
        }
    ],
    "limit": "20",
    "offset": 0,
    "order": "desc",
    "orderby": "created",
    "curr": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts?orderby=created&order=desc&limit=20&offset=0",
    "next": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts?orderby=created&order=desc&limit=20&offset=20",
    "prev": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts?orderby=created&order=desc&limit=20&offset=0",
    "total_count": 2
}
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/collections" method="post" summary="Create a collection" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="query" name="orderby" type="string" %}
Options: desc, asc
{% endswagger-parameter %}

{% swagger-parameter in="query" name="limit" type="string" %}
:number
{% endswagger-parameter %}

{% swagger-parameter in="body" name="user_id" type="string" %}
The owner of the collection
{% endswagger-parameter %}

{% swagger-parameter in="body" name="name" type="string" %}
Collection's name
{% endswagger-parameter %}

{% swagger-parameter in="body" name="role" type="array" %}
The role id that is allowed to see and use it
{% endswagger-parameter %}

{% swagger-parameter in="body" name="view" type="string" %}
Options: map,data
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "count": 2,
    "results": [
        {
            "id": 18100,
            "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts\/18100",
            "parent_id": null,
            "form": {
                "id": 2,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/forms\/2"
            },
            "user_id": null,
            "message": null,
            "color": null,
            "type": "report",
            "title": "401 edits",
            "slug": "401-5bfd5fce5eba2",
            "content": "Tests",
            "status": "published",
            "created": "2018-11-27T15:16:33+00:00",
            "updated": "2018-11-27T15:47:13+00:00",
            "locale": "en_us",
            "values": {
                "fc81397d-b73d-43a6-b1da-7614534563be": [
                    "2018-11-22 05:31:00"
                ],
                "cc0cb71a-c0af-4e0a-94ac-be6b21f4b796": [
                    "2018-11-27 15:46:50"
                ],
                "9b65d16f-023d-4c1c-9284-8a3d57c8ae0a": [
                    {
                        "lon": 9.563599,
                        "lat": 7.710992
                    }
                ]
            },
            "post_date": "2018-11-27T15:16:33+00:00",
            "tags": [],
            "published_to": [],
            "completed_stages": [],
            "sets": [
                "14"
            ],
            "lock": null,
            "source": null,
            "contact": null,
            "data_source_message_id": null,
            "allowed_privileges": [
                "read",
                "create",
                "search"
            ]
        },
        {
            "id": 8328,
            "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts\/8328",
            "parent_id": null,
            "form": {
                "id": 1,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1"
            },
            "user_id": null,
            "message": {
                "id": 23462,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/messages\/23462"
            },
            "color": null,
            "type": "report",
            "title": "Test 2.",
            "slug": "-58fdc11e54991",
            "content": "Some content",
            "status": "published",
            "created": "2017-04-24T09:10:54+00:00",
            "updated": "2017-04-24T13:10:19+00:00",
            "locale": "en_us",
            "values": [],
            "post_date": "2017-04-24T09:10:54+00:00",
            "tags": [],
            "published_to": [],
            "completed_stages": [
                1
            ],
            "sets": [
                "1",
                "3"
            ],
            "lock": null,
            "source": "sms",
            "contact": {
                "id": 2693,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/contact\/2693"
            },
            "data_source_message_id": null,
            "allowed_privileges": [
                "read",
                "create",
                "search"
            ]
        }
    ],
    "limit": "20",
    "offset": 0,
    "order": "desc",
    "orderby": "created",
    "curr": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts?orderby=created&order=desc&limit=20&offset=0",
    "next": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts?orderby=created&order=desc&limit=20&offset=20",
    "prev": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts?orderby=created&order=desc&limit=20&offset=0",
    "total_count": 2
}
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/collections/:collectionId" method="delete" summary="Delete a collection" %}
{% swagger-description %}
Delete the collection by its id.
{% endswagger-description %}

{% swagger-parameter in="path" name="collectionId" type="number" %}
The collection id.. can appear as set_id in other places
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="204" description="" %}
```
Success
```
{% endswagger-response %}

{% swagger-response status="404" description="" %}
```
The Collection id is incorrect
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/config" method="get" summary="" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="path" name="" type="string" %}

{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/contacts/:contactId" method="get" summary="" %}
{% swagger-description %}
Only used by deployments that require messaging capabilities such as receiving posts by SMS or Twitter.
{% endswagger-description %}

{% swagger-parameter in="path" name="contact_id" type="number" %}
The contact id that you want to retrieve
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "id": 1234,
    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/contacts\/1234",
    "user_id": null,
    "data_source": "twitter",
    "type": "twitter",
    "contact": "999888777",
    "created": "2018-12-06T17:15:29+00:00",
    "updated": null,
    "can_notify": false,
    "country_code": null,
    "allowed_privileges": [
        "read",
        "create",
        "update",
        "delete",
        "search"
    ]
}
```
{% endswagger-response %}

{% swagger-response status="404" description="" %}
```
Incorrect contact id
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/country-codes" method="get" summary="Get country codes" %}
{% swagger-description %}
This is only used to get a list of country codes that we can use for the UI of targeted surveys, a SaaS platform feature for sending surveys in steps to groups of people that they can respond to from their phones.
{% endswagger-description %}

{% swagger-parameter in="path" name="" type="string" %}

{% endswagger-parameter %}

{% swagger-parameter in="header" name="Auth" type="string" %}
Bearer <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="Example response with 5 countries and their code" %}
```
{
    "count": 246,
    "results": [
        {
            "id": 1,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/country_codes\/1",
            "country_name": "Afghanistan",
            "dial_code": "+93",
            "country_code": "AF",
            "allowed_privileges": [
                "read",
                "search"
            ]
        },
        {
            "id": 2,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/country_codes\/2",
            "country_name": "\u00c5land Islands",
            "dial_code": "+358",
            "country_code": "AX",
            "allowed_privileges": [
                "read",
                "search"
            ]
        },
        {
            "id": 3,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/country_codes\/3",
            "country_name": "Albania",
            "dial_code": "+355",
            "country_code": "AL",
            "allowed_privileges": [
                "read",
                "search"
            ]
        },
        {
            "id": 4,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/country_codes\/4",
            "country_name": "Algeria",
            "dial_code": "+213",
            "country_code": "DZ",
            "allowed_privileges": [
                "read",
                "search"
            ]
        },
        {
            "id": 5,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/country_codes\/5",
            "country_name": "American Samoa",
            "dial_code": "+1684",
            "country_code": "AS",
            "allowed_privileges": [
                "read",
                "search"
            ]
        }
    ],
    "limit": null,
    "offset": 0,
    "order": "asc",
    "orderby": "id",
    "curr": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/country-codes?orderby=id&order=asc&offset=0",
    "next": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/country-codes?orderby=id&order=asc&offset=0",
    "prev": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/country-codes?orderby=id&order=asc&offset=0",
    "total_count": 246
}
```
{% endswagger-response %}
{% endswagger %}

## CSV Exports and Imports

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/exports/jobs?user=me" method="get" summary="Get a list of CSV exports jobs" %}
{% swagger-description %}
This returns all the metadata for exports of the current user
{% endswagger-description %}

{% swagger-parameter in="path" name=":jobId" type="number" %}
Export ID
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="query" name="user" type="string" %}
Value: me
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/csv/:id/import" method="put" summary="[WIP]Create metadata to start a CSV Import" %}
{% swagger-description %}
This returns all the metadata for exports of the current user
{% endswagger-description %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="query" name="user" type="string" %}
Value: me
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
{
    "id": 19,
    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/csv\/19",
    "columns": [
        "Post ID",
        "Survey",
        "Post Status",
        "Created (UTC)",
        "Updated (UTC)",
        "Post Date (UTC)",
        "Contact ID",
        "Contact",
        "Unstructured Description",
        "Title",
        "Title",
        "Title",
        "Title",
        "Title"
    ],
    "maps_to": null,
    "fixed": null,
    "filename": "ushahididocs.api.ushahidi.io\/5\/c\/csv-export-full.csv",
    "mime": "text\/csv",
    "size": 2822,
    "created": "2018-12-06T19:51:15+00:00",
    "updated": null,
    "completed": null,
    "status": null,
    "errors": null,
    "processed": null,
    "collection_id": null,
    "allowed_privileges": [
        "read",
        "create",
        "update",
        "delete",
        "search"
    ]
}
```
{% endswagger-response %}
{% endswagger %}

## Dataproviders (Datasources in the UI, read only config)

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/dataproviders/[:id]" method="get" summary="Get data provider options" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="path" name="id" type="string" %}
Dataprovider id
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "count": 6,
    "results": [
        {
            "id": "email",
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/dataprovider\/email",
            "name": "Email",
            "services": [
                "email"
            ],
            "options": {
                "intro_text": {
                    "label": "",
                    "input": "read-only-text",
                    "description": "In order to receive posts by email, please input your email account settings below"
                },
                "incoming_type": {
                    "label": "Incoming Server Type",
                    "input": "radio",
                    "description": "",
                    "options": [
                        "POP",
                        "IMAP"
                    ],
                    "rules": [
                        "required",
                        "number"
                    ]
                },
                "incoming_server": {
                    "label": "Incoming Server",
                    "input": "text",
                    "description": "Examples: mail.yourwebsite.com, imap.gmail.com, pop.gmail.com",
                    "rules": [
                        "required"
                    ]
                },
                "incoming_port": {
                    "label": "Incoming Server Port",
                    "input": "text",
                    "description": "Common ports: 110 (POP3), 143 (IMAP), 995 (POP3 with SSL), 993 (IMAP with SSL)",
                    "rules": [
                        "required",
                        "number"
                    ]
                },
                "incoming_security": {
                    "label": "Incoming Server Security",
                    "input": "radio",
                    "description": "",
                    "options": [
                        "None",
                        "SSL",
                        "TLS"
                    ]
                },
                "incoming_username": {
                    "label": "Incoming Username",
                    "input": "text",
                    "description": "",
                    "placeholder": "Email account username",
                    "rules": [
                        "required"
                    ]
                },
                "incoming_password": {
                    "label": "Incoming Password",
                    "input": "text",
                    "description": "",
                    "placeholder": "Email account password",
                    "rules": [
                        "required"
                    ]
                }
            },
            "inbound_fields": {
                "Subject": "text",
                "Date": "datetime",
                "Message": "text"
            },
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": "frontlinesms",
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/dataprovider\/frontlinesms",
            "name": "FrontlineSMS",
            "services": [
                "sms"
            ],
            "options": {
                "key": {
                    "label": "Key",
                    "input": "text",
                    "description": "The API key",
                    "rules": [
                        "required"
                    ]
                },
                "secret": {
                    "label": "Secret",
                    "input": "text",
                    "description": "Set a secret so that only authorized FrontlineCloud accounts can send\/recieve message.\n\t\t\t\t\tYou need to configure the same secret in the FrontlineCloud Activity.",
                    "rules": [
                        "required"
                    ]
                }
            },
            "inbound_fields": {
                "Message": "text"
            },
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": "nexmo",
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/dataprovider\/nexmo",
            "name": "Nexmo",
            "services": [
                "sms"
            ],
            "options": {
                "from": {
                    "label": "From",
                    "input": "text",
                    "description": "The from number",
                    "rules": [
                        "required"
                    ]
                },
                "api_key": {
                    "label": "API Key",
                    "input": "text",
                    "description": "The API key",
                    "rules": [
                        "required"
                    ]
                },
                "api_secret": {
                    "label": "API secret",
                    "input": "text",
                    "description": "The API secret",
                    "rules": [
                        "required"
                    ]
                }
            },
            "inbound_fields": {
                "Message": "text"
            },
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": "smssync",
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/dataprovider\/smssync",
            "name": "SMSSync",
            "services": [
                "sms"
            ],
            "options": {
                "intro_step1": {
                    "label": "Step 1: Download the \"SMSSync\" app from the Android Market.",
                    "input": "read-only-text",
                    "description": "Scan this QR Code with your phone to download the app from the Android Market\n\t\t\t\t\t\t<img src=\"https:\/\/ushahididocs.api.ushahidi.io\/images\/smssync.png\" width=\"150\"\/>"
                },
                "intro_step2": {
                    "label": "Step 2: Android App Settings",
                    "input": "read-only-text",
                    "description": "Turn on SMSSync and use the following link as the Sync URL: https:\/\/ushahididocs.api.ushahidi.io\/sms\/smssync"
                },
                "secret": {
                    "label": "Secret",
                    "input": "text",
                    "description": "Set a secret so that only authorized SMSSync devices can send\/recieve message.\n\t\t\t\t\tYou need to configure the same secret in the SMSSync App.",
                    "rules": [
                        "required"
                    ]
                }
            },
            "inbound_fields": {
                "Message": "text",
                "Date": "datetime"
            },
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": "twilio",
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/dataprovider\/twilio",
            "name": "Twilio",
            "services": [
                "sms"
            ],
            "options": {
                "from": {
                    "label": "Phone Number",
                    "input": "text",
                    "description": "The from phone number.\n\t\t\t\t\tA Twilio phone number enabled for the type of message you wish to send. ",
                    "rules": [
                        "required"
                    ]
                },
                "account_sid": {
                    "label": "Account SID",
                    "input": "text",
                    "description": "The unique id of the Account that sent this message.",
                    "rules": [
                        "required"
                    ]
                },
                "auth_token": {
                    "label": "Auth Token",
                    "input": "text",
                    "description": "",
                    "rules": [
                        "required"
                    ]
                },
                "sms_auto_response": {
                    "label": "SMS Auto response",
                    "input": "text",
                    "description": "",
                    "rules": [
                        "required"
                    ]
                }
            },
            "inbound_fields": {
                "Message": "text"
            },
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": "twitter",
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/dataprovider\/twitter",
            "name": "Twitter",
            "services": [
                "twitter"
            ],
            "options": {
                "intro_step1": {
                    "label": "Step 1: Create a new Twitter application",
                    "input": "read-only-text",
                    "description": "Create a <a href=\"https:\/\/apps.twitter.com\/app\/new\">new twitter application<\/a>"
                },
                "intro_step2": {
                    "label": "Step 2: Generate a consumer key and secret",
                    "input": "read-only-text",
                    "description": "Once you've created the application click on \"Keys and Access Tokens\".<br \/>\n\t\t\t\t\t\tThen click \"Generate Consumer Key and Secret\".<br \/>\n\t\t\t\t\t\tCopy keys, tokens and secrets into the fields below."
                },
                "consumer_key": {
                    "label": "Consumer Key",
                    "input": "text",
                    "description": "Add the consumer key from your Twitter app. ",
                    "rules": [
                        "required"
                    ]
                },
                "consumer_secret": {
                    "label": "Consumer Secret",
                    "input": "text",
                    "description": "Add the consumer secret from your Twitter app.",
                    "rules": [
                        "required"
                    ]
                },
                "oauth_access_token": {
                    "label": "Access Token",
                    "input": "text",
                    "description": "Add the access token you generated for your Twitter app.",
                    "rules": [
                        "required"
                    ]
                },
                "oauth_access_token_secret": {
                    "label": "Access Token Secret",
                    "input": "text",
                    "description": "Add the access secret that you generated for your Twitter app.",
                    "rules": [
                        "required"
                    ]
                },
                "twitter_search_terms": {
                    "label": "Twitter search terms",
                    "input": "text",
                    "description": "Add search terms separated with commas",
                    "rules": [
                        "required"
                    ]
                }
            },
            "inbound_fields": {
                "Date": "datetime",
                "Message": "text"
            },
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        }
    ],
    "limit": null,
    "offset": 0,
    "order": "asc",
    "orderby": "id",
    "curr": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/dataproviders?orderby=id&order=asc&offset=0",
    "next": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/dataproviders?orderby=id&order=asc&offset=0",
    "prev": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/dataproviders?orderby=id&order=asc&offset=0",
    "total_count": 6
}
```
{% endswagger-response %}
{% endswagger %}

## Forms (Surveys)

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/forms" method="get" summary="Get all surveys" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "count": 6,
    "results": [
        {
            "id": 1,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1",
            "parent_id": null,
            "name": "Basic Post 2",
            "description": "Post with a location",
            "color": "#A51A1A",
            "type": "report",
            "disabled": false,
            "created": "2018-04-16T23:51:28+00:00",
            "updated": "2018-10-30T16:52:23+00:00",
            "hide_author": false,
            "hide_time": false,
            "hide_location": false,
            "require_approval": true,
            "QAryone_can_create": true,
            "targeted_survey": false,
            "can_create": [],
            "tags": [
                {
                    "id": 1,
                    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/1"
                },
                {
                    "id": 3,
                    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/3"
                },
                {
                    "id": 11,
                    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/11"
                },
                {
                    "id": 10,
                    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/10"
                },
                {
                    "id": 12,
                    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/12"
                },
                {
                    "id": 13,
                    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/13"
                }
            ],
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": 2,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/2",
            "parent_id": null,
            "name": "Data for export",
            "description": null,
            "color": null,
            "type": "report",
            "disabled": false,
            "created": "2018-04-17T03:24:51+00:00",
            "updated": null,
            "hide_author": false,
            "hide_time": false,
            "hide_location": false,
            "require_approval": true,
            "QAryone_can_create": true,
            "targeted_survey": false,
            "can_create": [],
            "tags": [],
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": 3,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/3",
            "parent_id": null,
            "name": "Some Testing",
            "description": "Regression Testing",
            "color": "#5BAA00",
            "type": "report",
            "disabled": false,
            "created": "2018-07-13T09:48:00+00:00",
            "updated": "2018-11-14T15:34:33+00:00",
            "hide_author": false,
            "hide_time": false,
            "hide_location": false,
            "require_approval": false,
            "QAryone_can_create": false,
            "targeted_survey": false,
            "can_create": [
                "admin",
                "QA Role"
            ],
            "tags": [],
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": 4,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/4",
            "parent_id": null,
            "name": "Another survey for api",
            "description": null,
            "color": null,
            "type": "report",
            "disabled": false,
            "created": "2018-08-10T18:10:57+00:00",
            "updated": "2018-08-29T18:47:11+00:00",
            "hide_author": false,
            "hide_time": false,
            "hide_location": false,
            "require_approval": true,
            "QAryone_can_create": true,
            "targeted_survey": false,
            "can_create": [],
            "tags": [],
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": 6,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/6",
            "parent_id": null,
            "name": "All fields example",
            "description": "",
            "color": null,
            "type": "report",
            "disabled": false,
            "created": "2018-08-31T15:33:38+00:00",
            "updated": "2018-08-31T16:19:42+00:00",
            "hide_author": false,
            "hide_time": false,
            "hide_location": false,
            "require_approval": true,
            "QAryone_can_create": true,
            "targeted_survey": false,
            "can_create": [],
            "tags": [
                {
                    "id": 1,
                    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/1"
                },
                {
                    "id": 3,
                    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/3"
                }
            ],
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        }
    ],
    "limit": null,
    "offset": 0,
    "order": "asc",
    "orderby": "id",
    "curr": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms?orderby=id&order=asc&offset=0",
    "next": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms?orderby=id&order=asc&offset=0",
    "prev": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms?orderby=id&order=asc&offset=0",
    "total_count": 6
}
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/forms/:id" method="get" summary="Get all options for one survey" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="path" name="id" type="number" %}
The survey id
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "id": 1,
    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1",
    "parent_id": null,
    "name": "Basic Post 2",
    "description": "Post with a location",
    "color": "#A51A1A",
    "type": "report",
    "disabled": false,
    "created": "2018-04-16T23:51:28+00:00",
    "updated": "2018-10-30T16:52:23+00:00",
    "hide_author": false,
    "hide_time": false,
    "hide_location": false,
    "require_approval": true,
    "everyone_can_create": true,
    "targeted_survey": false,
    "can_create": [],
    "tags": [
        {
            "id": 1,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/1"
        },
        {
            "id": 3,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/3"
        },
        {
            "id": 11,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/11"
        },
        {
            "id": 10,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/10"
        },
        {
            "id": 12,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/12"
        },
        {
            "id": 13,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/tags\/13"
        }
    ],
    "allowed_privileges": [
        "read",
        "create",
        "update",
        "delete",
        "search"
    ]
}
```
{% endswagger-response %}

{% swagger-response status="404" description="" %}
```
If the survey does not exist, a 404 will be raised
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/forms/:id/attributes" method="get" summary="Get all attributes (fields) for one survey" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="path" name="id" type="number" %}
The form id
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "count": 5,
    "results": [
        {
            "id": 1,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/form_attributes\/1",
            "key": "location_default",
            "label": "Location",
            "instructions": null,
            "input": "location",
            "type": "point",
            "required": false,
            "default": null,
            "priority": 0,
            "options": null,
            "cardinality": 1,
            "config": null,
            "form_stage_id": 1,
            "response_private": false,
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": 3,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/form_attributes\/3",
            "key": "ff068758-2b7e-4a3b-af14-acbe26284ed1",
            "label": "Title",
            "instructions": null,
            "input": "text",
            "type": "title",
            "required": true,
            "default": null,
            "priority": 0,
            "options": null,
            "cardinality": 0,
            "config": null,
            "form_stage_id": 1,
            "response_private": false,
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": 4,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/form_attributes\/4",
            "key": "794636ad-5333-44db-aa09-d0ed553c06d4",
            "label": "Description",
            "instructions": null,
            "input": "text",
            "type": "description",
            "required": true,
            "default": null,
            "priority": 0,
            "options": null,
            "cardinality": 0,
            "config": null,
            "form_stage_id": 1,
            "response_private": false,
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": 58,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/form_attributes\/58",
            "key": "4695fc1a-f51d-4d59-b264-97083e4e8179",
            "label": "Status",
            "instructions": null,
            "input": "tags",
            "type": "tags",
            "required": false,
            "default": null,
            "priority": 1,
            "options": [
                11,
                10,
                12,
                13
            ],
            "cardinality": 0,
            "config": [],
            "form_stage_id": 14,
            "response_private": false,
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": 5,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/form_attributes\/5",
            "key": "1bf39730-3019-4c49-a0c7-c988c519effa",
            "label": "Categories",
            "instructions": null,
            "input": "tags",
            "type": "tags",
            "required": false,
            "default": null,
            "priority": 3,
            "options": [
                1,
                3
            ],
            "cardinality": 0,
            "config": [],
            "form_stage_id": 1,
            "response_private": false,
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        }
    ],
    "limit": null,
    "offset": 0,
    "order": "asc",
    "orderby": "priority",
    "curr": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1\/attributes?orderby=priority&order=asc&offset=0",
    "next": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1\/attributes?orderby=priority&order=asc&offset=0",
    "prev": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1\/attributes?orderby=priority&order=asc&offset=0",
    "total_count": 5
}
```
{% endswagger-response %}

{% swagger-response status="404" description="" %}
```
If the survey does not exist, a 404 will be raised
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/forms/:id/stages" method="get" summary="Get all stages (groups of fields) for one survey" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="path" name="id" type="number" %}
The form id
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "count": 2,
    "results": [
        {
            "id": 1,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/form_stages\/1",
            "form_id": 1,
            "label": "Structure",
            "priority": 0,
            "icon": null,
            "type": "post",
            "required": false,
            "show_when_published": true,
            "description": null,
            "task_is_internal_only": false,
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        },
        {
            "id": 14,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/form_stages\/14",
            "form_id": 1,
            "label": "completion status",
            "priority": 1,
            "icon": null,
            "type": "task",
            "required": true,
            "show_when_published": true,
            "description": null,
            "task_is_internal_only": true,
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        }
    ],
    "limit": null,
    "offset": 0,
    "order": "asc",
    "orderby": "priority",
    "curr": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1\/stages?orderby=priority&order=asc&offset=0",
    "next": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1\/stages?orderby=priority&order=asc&offset=0",
    "prev": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1\/stages?orderby=priority&order=asc&offset=0",
    "total_count": 2
}
```
{% endswagger-response %}

{% swagger-response status="404" description="" %}
```
If the survey does not exist, a 404 will be raised
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/forms/:id/stats" method="get" summary="Get stats (usage data) for one survey" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="path" name="id" type="number" %}
The form id
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "id": null,
    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/form_stats",
    "total_responses": null,
    "total_recipients": null,
    "total_response_recipients": null,
    "total_messages_sent": null,
    "total_messages_pending": null,
    "total_by_data_source": {
        "sms": 0,
        "email": "2",
        "twitter": 0,
        "web": "199",
        "all": 201
    },
    "allowed_privileges": [
        "read",
        "create",
        "update",
        "delete",
        "search"
    ]
}
```
{% endswagger-response %}

{% swagger-response status="404" description="" %}
```
If the survey does not exist, a 404 will be raised
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/forms/:id/roles" method="get" summary="Get all roles assigned to one survey" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="path" name="id" type="number" %}
The form id
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "count": 1,
    "results": [
        {
            "id": 6,
            "url": "https:\/\/ushahididocs.api.ushahidi.io\/forms\/1\/roles\/6",
            "form_id": 7,
            "role_id": 4,
            "allowed_privileges": [
                "read",
                "create",
                "update",
                "delete",
                "search"
            ]
        }
    ],
    "limit": null,
    "offset": 0,
    "order": "asc",
    "orderby": "role_id",
    "curr": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1\/roles?orderby=role_id&order=asc&offset=0",
    "next": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1\/roles?orderby=role_id&order=asc&offset=0",
    "prev": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1\/roles?orderby=role_id&order=asc&offset=0",
    "total_count": 1
}
```
{% endswagger-response %}

{% swagger-response status="404" description="" %}
```
If the survey does not exist, a 404 will be raised
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/forms" method="post" summary="Create a survey" %}
{% swagger-description %}
Create a survey in the backend.
{% endswagger-description %}

{% swagger-parameter in="path" name="" type="string" %}

{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="body" name="name" type="string" %}
The survey name
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.id" type="string" %}
A frontend generated ID to identify it while its being created. Example interim_id_2
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.is_public" type="boolean" %}
Is the task open to everyone? Default: true
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.attributes" type="array" %}
A list of attributes for the task. Takes the same options as form attributes for each.
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.task_is_internal_only" type="boolean" %}
Default: false
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.show_when_published" type="boolean" %}
Default: true
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.type" type="string" %}
post
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.required" type="boolean" %}
Default: false
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.priority" type="number" %}
The order of the task in the form
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.label" type="string" %}
The stage label
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks" type="array" %}
Array of stages (tasks) with options. Look for tasks.$n.$x in this list of params to see the attributes available for tasks
{% endswagger-parameter %}

{% swagger-parameter in="body" name="everyone_can_create" type="string" %}
Can any user create posts? Default: true
{% endswagger-parameter %}

{% swagger-parameter in="body" name="require_approvel" type="boolean" %}
Does the data in this survey auto-publish or is it saved as draft (default: false)
{% endswagger-parameter %}

{% swagger-parameter in="body" name="color" type="string" %}
The survey color (shown in map and data view)
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
{
    "id": 11,
    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/11",
    "parent_id": null,
    "name": "The survey name",
    "description": null,
    "color": null,
    "type": "report",
    "disabled": false,
    "created": "2018-12-10T12:52:05+00:00",
    "updated": null,
    "hide_author": false,
    "hide_time": false,
    "hide_location": false,
    "require_approval": true,
    "everyone_can_create": true,
    "targeted_survey": false,
    "can_create": [],
    "tags": [],
    "allowed_privileges": [
        "read",
        "create",
        "update",
        "delete",
        "search"
    ]
}
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/forms/:form_id" method="put" summary="Update a survey (example adding a new field)" %}
{% swagger-description %}
Create a survey in the backend.
{% endswagger-description %}

{% swagger-parameter in="path" name="form_id" type="number" %}
The survey id.
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="body" name="name" type="string" %}
The survey name
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.id" type="string" %}
A frontend generated ID to identify it while its being created. Example interim_id_2
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.is_public" type="boolean" %}
Is the task open to everyone? Default: true
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.attributes" type="array" %}
A list of attributes for the task. Takes the same options as form attributes for each.
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.task_is_internal_only" type="boolean" %}
Default: false
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.show_when_published" type="boolean" %}
Default: true
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.type" type="string" %}
post
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.required" type="boolean" %}
Default: false
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.priority" type="number" %}
The order of the task in the form
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks.$n.label" type="string" %}
The stage label
{% endswagger-parameter %}

{% swagger-parameter in="body" name="tasks" type="array" %}
Array of stages (tasks) with options. Look for tasks.$n.$x in this list of params to see the attributes available for tasks
{% endswagger-parameter %}

{% swagger-parameter in="body" name="everyone_can_create" type="string" %}
Can any user create posts? Default: true
{% endswagger-parameter %}

{% swagger-parameter in="body" name="require_approvel" type="boolean" %}
Does the data in this survey auto-publish or is it saved as draft (default: false)
{% endswagger-parameter %}

{% swagger-parameter in="body" name="color" type="string" %}
The survey color (shown in map and data view)
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
{
    "id": 11,
    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/11",
    "parent_id": null,
    "name": "The survey name",
    "description": null,
    "color": null,
    "type": "report",
    "disabled": false,
    "created": "2018-12-10T12:52:05+00:00",
    "updated": null,
    "hide_author": false,
    "hide_time": false,
    "hide_location": false,
    "require_approval": true,
    "everyone_can_create": true,
    "targeted_survey": false,
    "can_create": [],
    "tags": [],
    "allowed_privileges": [
        "read",
        "create",
        "update",
        "delete",
        "search"
    ]
}
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/forms/:form_id" method="delete" summary="Delete a survey" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="path" name="form_id" type="number" %}
The survey id.
{% endswagger-parameter %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
{
    "id": 11,
    "url": "https:\/\/ushahididocs.api.ushahidi.io\/api\/v3\/forms\/11",
    "parent_id": null,
    "name": "A survey created to delete it",
    "description": null,
    "color": null,
    "type": "report",
    "disabled": false,
    "created": "2018-12-17T21:29:13+00:00",
    "updated": null,
    "hide_author": false,
    "hide_time": false,
    "hide_location": false,
    "require_approval": true,
    "everyone_can_create": true,
    "targeted_survey": false,
    "can_create": [],
    "tags": [],
    "allowed_privileges": [
        "read",
        "create",
        "update",
        "delete",
        "search"
    ]
}
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/messages" method="post" summary="Respond to datasource messages" %}
{% swagger-description %}
Used in the "Conversation with author" UX in the platform.
{% endswagger-description %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="body" name="parent_id" type="number" %}
The id of the message you are responding to.
{% endswagger-parameter %}

{% swagger-parameter in="body" name="contact_id" type="number" %}
The contact id of the person you want to send a message to. You can get this in the "Get messages for a post contact" endpoint below this one.
{% endswagger-parameter %}

{% swagger-parameter in="body" name="direction" type="string" %}
Use "outgoing" for sending messages. We use "incoming" when a datasource fetches a message.
{% endswagger-parameter %}

{% swagger-parameter in="body" name="message" type="string" %}
The message you want to send
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/messages" method="get" summary="Get all messages for a post's contact" %}
{% swagger-description %}
Used in the "Conversation with author" UX in the platform.
{% endswagger-description %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="query" name="orderby" type="string" %}
Options: "created", "updated"
{% endswagger-parameter %}

{% swagger-parameter in="query" name="order" type="string" %}
Options: "desc", "asc"
{% endswagger-parameter %}

{% swagger-parameter in="query" name="limit" type="number" %}
Pagination limit
{% endswagger-parameter %}

{% swagger-parameter in="query" name="offset" type="number" %}
Pagination offset
{% endswagger-parameter %}

{% swagger-parameter in="query" name="contact" type="number" %}
The contact id. You can get it from the post the message is linked to.
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/messages/:messageId/post" method="get" summary="Get the post for a message" %}
{% swagger-description %}
Used in the "Conversation with author" UX in the platform.
{% endswagger-description %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="query" name="orderby" type="string" %}
Options: "created", "updated"
{% endswagger-parameter %}

{% swagger-parameter in="query" name="order" type="string" %}
Options: "desc", "asc"
{% endswagger-parameter %}

{% swagger-parameter in="query" name="limit" type="number" %}
Pagination limit
{% endswagger-parameter %}

{% swagger-parameter in="query" name="offset" type="number" %}
Pagination offset
{% endswagger-parameter %}

{% swagger-parameter in="query" name="contact" type="number" %}
The contact id. You can get it from the post the message is linked to.
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```
```
{% endswagger-response %}
{% endswagger %}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/posts" method="post" summary="Create a new post" %}
{% swagger-description %}
Create a new post in the ushahidi platform. This method works with a user's password_grant token or with a client_credentials token generated with the client id and secret.
{% endswagger-description %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer: <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="body" name="form" type="object" %}
Format: {id: <formId>} . Sending the id of the form we want to add posts to is required.
{% endswagger-parameter %}

{% swagger-parameter in="body" name="values" type="object" %}
a key:value map of fields and their content. This is used for all fields other than content and title and follows the format fieldKey: value. You can get a field's get by requesting all attributes of a form. Can be an empty object literal if a form has no other fields.
{% endswagger-parameter %}

{% swagger-parameter in="body" name="content" type="string" %}
The post's description field
{% endswagger-parameter %}

{% swagger-parameter in="body" name="title" type="string" %}
The post's title field
{% endswagger-parameter %}

{% swagger-response status="204" description="" %}
```
Success
```
{% endswagger-response %}
{% endswagger %}

Example payload:

{"title":"My title","content":"My content","values":{},"form":{"id":4\}}

{% swagger baseUrl="https://ushahididocs.api.ushahidi.io" path="/api/v3/posts" method="get" summary="Get Posts" %}
{% swagger-description %}

{% endswagger-description %}

{% swagger-parameter in="header" name="Authorization" type="string" %}
Bearer <your-auth-token>
{% endswagger-parameter %}

{% swagger-parameter in="query" name="orderby" type="string" %}
Options: desc, asc
{% endswagger-parameter %}

{% swagger-parameter in="query" name="limit" type="string" %}
:number
{% endswagger-parameter %}

{% swagger-response status="200" description="" %}
```javascript
{
    "count": 2,
    "results": [
        {
            "id": 18100,
            "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts\/18100",
            "parent_id": null,
            "form": {
                "id": 2,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/forms\/2"
            },
            "user_id": null,
            "message": null,
            "color": null,
            "type": "report",
            "title": "401 edits",
            "slug": "401-5bfd5fce5eba2",
            "content": "Tests",
            "status": "published",
            "created": "2018-11-27T15:16:33+00:00",
            "updated": "2018-11-27T15:47:13+00:00",
            "locale": "en_us",
            "values": {
                "fc81397d-b73d-43a6-b1da-7614534563be": [
                    "2018-11-22 05:31:00"
                ],
                "cc0cb71a-c0af-4e0a-94ac-be6b21f4b796": [
                    "2018-11-27 15:46:50"
                ],
                "9b65d16f-023d-4c1c-9284-8a3d57c8ae0a": [
                    {
                        "lon": 9.563599,
                        "lat": 7.710992
                    }
                ]
            },
            "post_date": "2018-11-27T15:16:33+00:00",
            "tags": [],
            "published_to": [],
            "completed_stages": [],
            "lock": null,
            "source": null,
            "contact": null,
            "data_source_message_id": null,
            "allowed_privileges": [
                "read",
                "create",
                "search"
            ]
        },
        {
            "id": 8328,
            "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts\/8328",
            "parent_id": null,
            "form": {
                "id": 1,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/forms\/1"
            },
            "user_id": null,
            "message": {
                "id": 23462,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/messages\/23462"
            },
            "color": null,
            "type": "report",
            "title": "Test 2.",
            "slug": "-58fdc11e54991",
            "content": "Some content",
            "status": "published",
            "created": "2017-04-24T09:10:54+00:00",
            "updated": "2017-04-24T13:10:19+00:00",
            "locale": "en_us",
            "values": [],
            "post_date": "2017-04-24T09:10:54+00:00",
            "tags": [],
            "published_to": [],
            "completed_stages": [
                1
            ],
            "lock": null,
            "source": "sms",
            "contact": {
                "id": 2693,
                "url": "https://ushahididocs.api.ushahidi.io\/api\/v3\/contact\/2693"
            },
            "data_source_message_id": null,
            "allowed_privileges": [
                "read",
                "create",
                "search"
            ]
        }
    ],
    "limit": "20",
    "offset": 0,
    "order": "desc",
    "orderby": "created",
    "curr": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts?orderby=created&order=desc&limit=20&offset=0",
    "next": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts?orderby=created&order=desc&limit=20&offset=20",
    "prev": "https://ushahididocs.api.ushahidi.io\/api\/v3\/posts?orderby=created&order=desc&limit=20&offset=0",
    "total_count": 2
}
```
{% endswagger-response %}
{% endswagger %}
