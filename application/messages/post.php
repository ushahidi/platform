<?php defined('SYSPATH') OR die('No direct script access.');

return [
	'tagDoesNotExist' => 'tag :value does not exist',
	'attributeDoesNotExist' => 'attribute ":param1" does not exist',
	'tooManyValues' => 'Too many values for :param1 (max: :param2)',
	'valueDoesNotExist' => 'value id :param2 for field :param1 does not exist',
	'canNotUseExistingValueOnNewPost' => 'Cannot use existing value :param2 for field :param1 on a new post',
	'attributeRequired' => 'attribute :param1 is required',
	'emptyIdAndLocale' => 'Must have at least id or locale',
	'emptyParentWithLocale' => 'Must have at parent id when passing locale',
	'notAnArray' => 'Post values for :param1 must be an array',
	'scalar' => 'Post values for :param1 must be scalar',
	'doesTranslationExist' => 'Translation :value for post :param2 already exists'
];
