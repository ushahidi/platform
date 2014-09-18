<?php defined('SYSPATH') OR die('No direct script access.');

return [
	'tagDoesNotExist' => 'tag ":tag" does not exist',
	'attributeDoesNotExist' => 'attribute ":key" does not exist',
	'tooManyValues' => 'Too many values for :key (max: :cardinality)',
	'valueDoesNotExist' => 'value id :id for field :key does not exist',
	'attributeRequired' => 'attribute :key is required',
	'emptyIdAndLocale' => 'Must have at least id or locale',
	'emptyParentWithLocale' => 'Must have at parent id when passing locale',
];
