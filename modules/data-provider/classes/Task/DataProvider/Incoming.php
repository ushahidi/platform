<?php defined('SYSPATH') or die('No direct access allowed');

/**
 * Send incoming messages
 *
 * Available config options are:
 *
 * --provider=provider
 *
 *   The provider to process messages for. Default: all
 *
 * --limit=limit
 *
 *   Number of messages to fetch at one time (per provider)
 *
 */
class Task_DataProvider_Incoming extends DataProvider_Task_DataProvider_Incoming {}