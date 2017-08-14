<?php

namespace Ushahidi\App\DataSource;

/**
 * Base class for all Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
interface DataSource {

	/**
	 * Constructor function for DataSource
	 */
	public function __construct(array $config);

	public function getName();
	public function getServices();
	public function getOptions();

	/**
	 * @param  string  to Phone number to receive the message
	 * @param  string  message Message to be sent
	 * @param  string  title   Message title
	 * @return array   Array of message status, and tracking ID.
	 */
	public function send($to, $message, $title = "");

	/**
	 * Fetch messages from provider
	 *
	 * For services where we have to poll for message (Twitter, Email, FrontlineSMS) this should
	 * poll the service and pass messages to $this->receive()
	 *
	 * @param  boolean $limit   maximum number of messages to fetch at a time
	 * @return int              number of messages fetched
	 */
	public function fetch($limit = FALSE);

	/**
	 * Transforming incoming webhooks requests into Messages
	 *
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	public function receive($request);

	/**
	 * Format output messages for polled sending
	 *
	 * @param  [type] $messages [description]
	 * @return [type]           [description]
	 */
	public function format($messages);

}
