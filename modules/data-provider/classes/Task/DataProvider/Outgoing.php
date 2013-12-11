<?php defined('SYSPATH') or die('No direct access allowed');

/**
 * Send outgoing messages
 *
 * Available config options are:
 *
 * --provider=provider
 *
 *   The provider to process messages for. Default: all
 *
 * --limit=limit
 *
 *   Number of messages to send at one time
 *
 */
class Task_DataProvider_Outgoing extends DataProvider_Task_DataProvider_Outgoing {}