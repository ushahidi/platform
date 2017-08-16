<?php

namespace Ushahidi\App\DataSource\Nexmo;

/**
 * Nexmo Data Provider
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Nexmo
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

use Ushahidi\App\DataSource\DataSource;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\Core\Entity\Contact;
use Log;
use Illuminate\Http\Request;

class Nexmo implements DataSource
{

	protected $config;

	/**
	 * Constructor function for DataSource
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	public function getName()
    {
		return 'Nexmo';
	}

	public function getServices()
	{
		return [MessageType::SMS];
	}

	public function getOptions()
	{
		return array(
			'from' => array(
				'label' => 'From',
				'input' => 'text',
				'description' => 'The from number',
				'rules' => array('required')
			),
			'secret' => array(
				'label' => 'Secret',
				'input' => 'text',
				'description' => 'The secret value',
				'rules' => array('required')
			),
			'api_key' => array(
				'label' => 'API Key',
				'input' => 'text',
				'description' => 'The API key',
				'rules' => array('required')
			),
			'api_secret' => array(
				'label' => 'API secret',
				'input' => 'text',
				'description' => 'The API secret',
				'rules' => array('required')
			)
		);
	}

	/**
	 * Client to talk to the Nexmo API
	 *
	 * @var NexmoMessage
	 */
	private $client;

	/**
	 * Sets the FROM parameter for the provider
	 *
	 * @return int
	 */
	public function from()
	{
		// Get provider phone (FROM)
		// Replace non-numeric
		return preg_replace('/\D+/', "", parent::from());
	}

	/**
	 * @return mixed
	 */
	public function send($to, $message, $title = "")
	{
		include_once __DIR__ . '/nexmo/NexmoMessage';

		if (! isset($this->client)) {
			$this->client = new \NexmoMessage($this->_options['api_key'], $this->_options['api_secret']);
		}

		// Send!
		try {
			$info = $this->client->sendText('+'.$to, '+'.preg_replace("/[^0-9,.]/", "", $this->from()), $message);
			foreach ($info->messages as $message) {
				if ($message->status != 0) {
					Log::warning('Nexmo: '.$message->errortext);
					return array(DataSource\Message\Status::FAILED, false);
				}

				return array(DataSource\Message\Status::SENT, $message->messageid);
			}
		} catch (Exception $e) {
			Log::warning($e->getMessage());
		}

		return array(DataSource\Message\Status::FAILED, false);
	}

	// DataSource
	public function fetch($limit = false)
    {
		return false;
	}

	// DataSource
	public function receive(Request $request)
    {
		include_once __DIR__ . '/vendor/nexmo/NexmoMessage.php';

		// Pong Sender
		$ipAddress = $request->getClientIp();
		$continue = false;
		foreach ($this->subnets as $subnet) {
			if (($this->ipInRange($ipAddress, $subnet))) {
				return abort(403, 'IP Address not in allowed range');
			};
		}

		if (! isset($this->config['api_key'])) {
			throw abort(403, 'Missing API key');
		}

		if (! isset($this->config['api_secret'])) {
			throw abort(403, 'Missing API secret');
		}

		$sms = new \NexmoMessage($this->config['api_key'], $this->config['api_secret']);

		if (! $sms->inboundText()) {
			throw abort(400, "Invalid message");
		}

		// Remove Non-Numeric characters because that's what the DB has
		$to = preg_replace("/[^0-9,.]/", "", $sms->to);
		$from  = preg_replace("/[^0-9,.]/", "", $sms->from);

		return [
			'type' => MessageType::SMS,
			'from' => $from,
			'contact_type' => Contact::PHONE,
			'message' => $sms->text,
			'to' => $to,
			'title' => null,
			'data_provider_message_id' => $sms->message_id,
			'data_provider' => 'nexmo'
		];
	}

	// Nexmo Subnets
	// To Restrict Inbound Callback
	private $subnets = [
		'174.37.245.32/29',
		'174.37.245.32/29',
		'174.36.197.192/28',
		'173.193.199.16/28',
		'119.81.44.0/28'
	];

	/**
	 * ip_in_range
	 *
	 * This function takes 2 arguments, an IP address and a "range" in several
	 * different formats.
	 * Network ranges can be specified as:
	 * 1. Wildcard format:     1.2.3.*
	 * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
	 * 3. Start-End IP format: 1.2.3.0-1.2.3.255
	 * The function will return true if the supplied IP is within the range.
	 * Note little validation is done on the range inputs - it expects you to
	 * use one of the above 3 formats.
	 *
	 * Copyright 2008: Paul Gregg <pgregg@pgregg.com>
	 * 10 January 2008
	 * Version: 1.2
	 *
	 * Source website: http://www.pgregg.com/projects/php/ip_in_range/
	 * Version 1.2
	 *
	 * This software is Donationware - if you feel you have benefited from
	 * the use of this tool then please consider a donation. The value of
	 * which is entirely left up to your discretion.
	 * http://www.pgregg.com/donate/
	 *
	 * Please do not remove this header, or source attibution from this file.
	 *
	 * @param string $ip - the ip address
	 * @param string $range - the range we're comparing against
	 **/
	private function ipInRange($ip, $range)
	{
		if (strpos($range, '/') !== false) {
			// $range is in IP/NETMASK format
			list($range, $netmask) = explode('/', $range, 2);
			if (strpos($netmask, '.') !== false) {
				// $netmask is a 255.255.0.0 format
				$netmask = str_replace('*', '0', $netmask);
				$netmask_dec = ip2long($netmask);
				return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
			} else {
				// $netmask is a CIDR size block
				// fix the range argument
				$x = explode('.', $range);
				while (count($x)<4) {
                    $x[] = '0';
                }
				list($a,$b,$c,$d) = $x;
				$range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b, empty($c)?'0':$c, empty($d)?'0':$d);
				$range_dec = ip2long($range);
				$ip_dec = ip2long($ip);

				# Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
				#$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

				# Strategy 2 - Use math to create it
				$wildcard_dec = pow(2, (32-$netmask)) - 1;
				$netmask_dec = ~ $wildcard_dec;

				return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
			}
		} else {
			// range might be 255.255.*.* or 1.2.3.0-1.2.3.255
			if (strpos($range, '*') !==false) { // a.b.*.* format
				// Just convert to A-B format by setting * to 0 for A and 255 for B
				$lower = str_replace('*', '0', $range);
				$upper = str_replace('*', '255', $range);
				$range = "$lower-$upper";
			}

			if (strpos($range, '-')!==false) { // A-B format
				list($lower, $upper) = explode('-', $range, 2);
				$lower_dec = (float)sprintf("%u", ip2long($lower));
				$upper_dec = (float)sprintf("%u", ip2long($upper));
				$ip_dec = (float)sprintf("%u", ip2long($ip));
				return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
			}

			Kohana::$log->add(
				Log::ERROR,
				'IP Address Range argument is not in 1.2.3.4/24 or 1.2.3.4/255.255.255.0 format'
			);

			return false;
		}
	}

	// DataSource
	public function format($messages)
    {
		return false;
	}

	public function registerRoutes($app)
	{
		// $app->post('sms/nexmo', 'Ushahidi\App\DataSource\Nexmo\Controller\Nexmo@reply');
		// $app->post('sms/nexmo/reply', 'Ushahidi\App\DataSource\Nexmo\Controller\Nexmo@reply');
		// $app->post('nexmo', 'Ushahidi\App\DataSource\Nexmo\Controller\Nexmo@reply');
	}
}
