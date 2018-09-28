<?php

return [
    'dsn' => env('SENTRY_DSN', false) ?: env('RAVEN_URL', false),

    // capture release as git sha
    // 'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),
    'release' => env('SENTRY_RELEASE', false),

    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,

    // Capture default user context
    'user_context' => false, // Disabled because it causes requests to fail w/ 401

    'processors' => [
        'Raven_Processor_SanitizeHttpHeadersProcessor',
        'Raven_Processor_SanitizeDataProcessor'
    ],
    'processorOptions' => [
        'Raven_Processor_SanitizeDataProcessor' => [
            // @codingStandardsIgnoreLine
            'fields_re' => '/(authorization|password|passwd|secret|password_confirmation|card_number|auth_pw|authToken|api_key|client_secret)/i',
        ],
        'Raven_Processor_SanitizeHttpHeadersProcessor' => [
            'sanitize_http_headers' => [
                'Authorization',
                'Proxy-Authorization',
                'X-Csrf-Token',
                'X-CSRFToken',
                'X-XSRF-TOKEN',
                'X-Ushahidi-Signature',
            ]
        ]
    ]
];
