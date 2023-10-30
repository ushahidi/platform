<?php

/**
 * Feature Config
 *
 */

return [
    // Determines which features are available in a deployment

    // Post views
    'views' => [
        'data' => true,
        'map' => true,
        'list' => true,
        'chart' => true,
        'timeline' => true,
        'activity' => true,
        'plan' => false,
    ],

    // Data Providers
    'data-providers' => [
        'email' => true,
        'smssync' => true,
        'twitter' => true,
        'frontlinesms' => true,
        'outgoingemail' => true,
        'twilio' => true,
        'nexmo' => true,
        'gmail' => false,
        'mteja' => false,
        'africastalking' => false,
        'httpsms' => false,
        'infobip' => false,
    ],

    // Client limits
    // Where TRUE is infinite and an integer defines a limit
    'limits' => [
        'posts' => true,
        'forms' => true,
        'admin_users' => true,
    ],

    // Private deployments
    'private' => [
        'enabled' => true,
    ],

    // Disable Registration
    'disable_registration' => [
        'enabled' => true,
    ],

    // Roles
    'roles' => [
        'enabled' => true,
    ],

    // Webhooks
    'webhooks' => [
        'enabled' => true,
    ],

    // Data import
    'data-import' => [
        'enabled' => true,
    ],

    // Targeted Surveys
    'targeted-surveys' => [
        'enabled' => false,
    ],

    'csv-speedup' => [
        'enabled' => false,
    ],

    // Enable or disable HXL export to HDX
    // We will need a new 'hxl-download' flag when we do the HXL downloads for P1
    'hxl' => [
        'enabled' => true,
    ],

    // Enable or disable User Settings feature
    'user-settings' => [
        'enabled' => true,
    ],

    // Enable or disable the Anonymisation of Reporters
    // Controls whether users can set obfuscation of location, redaction of date/time
    // and reporter info
    'anonymise-reporters' => [
        'enabled' => true,
    ],

    // Enable or disable donations via web monetizations
    'donation' => [
        'enabled' => true
    ],

    // Enable or disable Gmail Support
    // Controls whether the users can set gmail credentials through the datasource config
    //   true: Gmail API credentials are provided via system environment (GMAIL_CLIENT_ID and GMAIL_CLIENT_SECRET)
    //   false: Credentials expected via the datasource configuration API
    'gmail-support' => [
        'enabled' => filter_var(getenv('GMAIL_SUPPORT_PROVIDED'), FILTER_VALIDATE_BOOLEAN) ?? false
    ]
];
