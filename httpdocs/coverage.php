<?php

require_once __DIR__ . '/../vendor/autoload.php';

$coverage = new \SebastianBergmann\CodeCoverage\CodeCoverage;
$coverage->setAddUncoveredFilesFromWhitelist(true);
$coverage->filter()->addDirectoryToWhitelist(__DIR__ . '/../application/');
$coverage->filter()->addDirectoryToWhitelist(__DIR__ . '/../src/');
$coverage->filter()->addDirectoryToWhitelist(__DIR__ . '/../plugins/*/classes/');
$coverage->start('behat-api-test');

// Initialize the Kohana application
include __DIR__ . '/index.php';

$coverage->stop();

$file = __DIR__ . '/../coverage/behat-' . uniqid() . '.xml';
$writer = new \SebastianBergmann\CodeCoverage\Report\Clover;
$writer->process($coverage, $file);
