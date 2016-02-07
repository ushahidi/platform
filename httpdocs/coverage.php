<?php

require __DIR__ . '/../vendor/autoload.php';

$coverage = new PHP_CodeCoverage;
$coverage->filter()->addDirectoryToBlacklist(__DIR__ . '../vendor/');
$coverage->start('behat-api-test');

// Initialize the Kohana application
include __DIR__ . '/index.php';

$coverage->stop();

$file = tempnam(__DIR__ . '/../coverage/', 'behat-');
$writer = new PHP_CodeCoverage_Report_Clover;
$writer->process($coverage, $file);
