<?php

if ( ! class_exists('Kohana'))
{
	die('Please include the kohana bootstrap file (see README.markdown)');
}

if ($file = Kohana::find_file('classes', 'unittest/tests'))
{
	require_once $file;

	// PHPUnit requires a test suite class to be in this file,
	// so we create a faux one that uses the kohana base
	Class TestSuite extends Unittest_Tests
	{}
}
else
{
	die('Could not include the test suite');
}