<?php defined('SYSPATH') OR die('No direct script access.');

return [
  'publishedPostsLimitReached' => 'limit::posts',
	'tagDoesNotExist' => 'tag :value does not exist',
	'attributeDoesNotExist' => 'attribute ":param1" does not exist',
	'tooManyValues' => 'Too many values for :param1 (max: :param2)',
	'valueDoesNotExist' => 'value id :param2 for field :param1 does not exist',
	'canNotUseExistingValueOnNewPost' => 'Cannot use existing value :param2 for field :param1 on a new post',
	'attributeRequired' => 'attribute :param1 is required before stage ":param2" can be completed',
	'emptyIdAndLocale' => 'Must have at least id or locale',
	'emptyParentWithLocale' => 'Must have at parent id when passing locale',
	'notAnArray' => 'Post values for :param1 must be an array',
	'scalar' => 'Post values for :param1 must be scalar',
	'doesTranslationExist' => 'Translation :value for post :param2 already exists',
	'isSlugAvailable' => ':field :value is already in use',
	'published_to' => [
		'exists' => 'The role you are publishing to ":value" does not exist'
	],
	'stageDoesNotExist' => 'Stage ":param1" does not exist',
	'stageRequired' => 'Stage ":param1" is required before publishing',

	'values' => [
		'date'          => 'The field :param1 must be a date, Given: :param2',
		'decimal'       => 'The field :param1 must be a decimal with 2 places, Given: :param2',
		'digit'         => 'The field :param1 must be a digit, Given: :param2',
		'email'         => 'The field :param1 must be an email address, Given: :param2',
		'exists'        => 'The field :param1 must be a valid post id, Post id: :param2',
		'max_length'    => 'The field :param1 must not exceed :param2 characters long, Given: :param2',
		'invalidForm'   => 'The field :param1 has the wrong post type, Post id: :param2',
		'numeric'       => 'The field :param1 must be numeric, Given: :param2',
		'scalar'        => 'The field :param1 must be scalar, Given: :param2',
		'point'         => 'The field :param1 must be an array of lat and lon',
		'url'           => 'The field :param1 must be a url, Given: :param2',
	]
];
