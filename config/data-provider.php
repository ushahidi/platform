<?php

/**
 * Data Provider Settings
 */

return [
    'providers' => [
        // List of data providers key=provider value=enabled
        'smssync' => true,
        'email' => true,
        'outgoingemail' => false,
        'twilio' => false,
        'nexmo' => false,
        'twitter' => false,
        'frontlinesms' => false,
        'gmail' => false,
        'mteja' => false,
        'africastalking' => false,
    ],

    'authenticable-providers' => [
        'gmail' => true
    ],

    'email' => [
        'incoming_type' => '',
        'incoming_server' => '',
        'incoming_port' => '',
        'incoming_security' => '',
        'incoming_username' => '',
        'incoming_password' => '',
        'incoming_unread_only' => 'Unread',
        'incoming_last_uid' => '0'
    ],
    'twilio' => [],
    'smssync' => [],
    'twitter' => [],
    'nexmo' => [],
    'frontlinesms' => [],
    'gmail' => [
        'redirect_uri' => 'urn:ietf:wg:oauth:2.0:oob',
        'authenticated' => false
    ],
];
