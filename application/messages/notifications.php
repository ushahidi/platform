<?php defined('SYSPATH') OR die('No direct script access.');

return [
	'email' => [
		'title' => '[:sitename] New post: :title',
		'message' => "New post on :sitename\n\n:title\n\n:content\n\nView post: :url"
	],
	'sms' => [
		'title' => '',
		'message' => '[:sitename] New post: :title',
	]
];
