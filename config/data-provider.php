<?php

/**
 * Data Provider Settings
 */

return [
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
