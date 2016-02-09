<?php

require __DIR__ . '/../vendor/autoload.php';

$coverage = new PHP_CodeCoverage;
$coverage->filter()->addDirectoryToBlacklist(__DIR__ . '../vendor/');
$coverage->start('behat-api-test');

// Initialize the Kohana application
include __DIR__ . '/index.php';

$coverage->stop();

$file = __DIR__ . '/../coverage/behat-' . uniqid() . '.xml';
$writer = new PHP_CodeCoverage_Report_Clover;
$writer->process($coverage, $file);
