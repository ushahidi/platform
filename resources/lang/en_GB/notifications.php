<?php

return [
    'email' => [
        'title' => '[:sitename] New post: :title',
        'message' => "New post on :sitename\n\n:title\n\n:content\n\nView post: :url",
    ],
    'sms' => [
        'title' => '',
        'message' => 'New post on :sitename\n\n:title\n\n:content\n\nView post: :url',
    ],
];
