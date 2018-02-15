<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Nexmo SMS Callback Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Nexmo
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Controller_Sms_Nexmo extends Controller {

	// Nexmo Subnets
	// To Restrict Inbound Callback
	private $subnets = array('174.37.245.32/29', '174.37.245.32/29', '174.36.197.192/28', '173.193.199.16/28', '119.81.44.0/28');

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
	private function _ip_in_range($ip, $range)
	{
		if (strpos($range, '/') !== false)
		{
			// $range is in IP/NETMASK format
			list($range, $netmask) = explode('/', $range, 2);
			if (strpos($netmask, '.') !== false)
			{
				// $netmask is a 255.255.0.0 format
				$netmask = str_replace('*', '0', $netmask);
				$netmask_dec = ip2long($netmask);
				return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
			}
			else
			{
				// $netmask is a CIDR size block
				// fix the range argument
				$x = explode('.', $range);
				while(count($x)<4) $x[] = '0';
				list($a,$b,$c,$d) = $x;
				$range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
				$range_dec = ip2long($range);
				$ip_dec = ip2long($ip);

				# Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
				#$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

				# Strategy 2 - Use math to create it
				$wildcard_dec = pow(2, (32-$netmask)) - 1;
				$netmask_dec = ~ $wildcard_dec;

				return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
			}
		}
		else
		{
			// range might be 255.255.*.* or 1.2.3.0-1.2.3.255
			if (strpos($range, '*') !==false)
			{ // a.b.*.* format
				// Just convert to A-B format by setting * to 0 for A and 255 for B
				$lower = str_replace('*', '0', $range);
				$upper = str_replace('*', '255', $range);
				$range = "$lower-$upper";
			}

			if (strpos($range, '-')!==false)
			{ // A-B format
				list($lower, $upper) = explode('-', $range, 2);
				$lower_dec = (float)sprintf("%u",ip2long($lower));
				$upper_dec = (float)sprintf("%u",ip2long($upper));
				$ip_dec = (float)sprintf("%u",ip2long($ip));
				return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
			}

			Kohana::$log->add(Log::ERROR, 'IP Address Range argument is not in 1.2.3.4/24 or 1.2.3.4/255.255.255.0 format');

			return false;
		}
	}

	/**
	 * Handle SMS from Nexmo
	 */
	public function action_reply()
	{
		include_once Kohana::find_file('vendor', 'nexmo/NexmoMessage');

    //Check if data provider is available
    $providers_available = Kohana::$config->load('features.data-providers');

    if ( !$providers_available['nexmo'] )
    {
      throw HTTP_Exception::factory(403, 'The Nexmo data source is not currently available. It can be accessed by upgrading to a higher Ushahidi tier.');
    }


		// Pong Sender
		$ip_address = $_SERVER["REMOTE_ADDR"];
		$continue = FALSE;
		foreach ($this->subnets as $subnet)
		{
			if ( ($this->_ip_in_range($ip_address, $subnet)) )
			{
				throw HTTP_Exception::factory(403, 'IP Address not in allowed range');
				break;
			};
		}

		$provider = DataProvider::factory('nexmo');
		$options = $provider->options();

		if( ! isset($options['api_key']))
		{
			throw HTTP_Exception::factory(403, 'Missing API key');
		}

		if ( ! isset($options['api_secret']))
		{
			throw HTTP_Exception::factory(403, 'Missing API secret');
		}

		$sms = new NexmoMessage($options['api_key'], $options['api_secret']);

		if ( ! $sms->inboundText())
		{
			throw HTTP_Exception::factory(400, "Invalid message");
		}

		// Remove Non-Numeric characters because that's what the DB has
		$to = preg_replace("/[^0-9,.]/", "", $sms->to);
		$from  = preg_replace("/[^0-9,.]/", "", $sms->from);

		// Check if a form id is already associated with this data provider
		$additional_data = [];
		if (isset($options['form_id'])) {
			$additional_data['form_id'] = $options['form_id'];
			$additional_data['inbound_fields'] = isset($options['inbound_fields']) ? $options['inbound_fields'] : NULL;
		}

		$provider->receive(Message_Type::SMS, $from, $sms->text, $to, $date = NULL, NULL, $sms->message_id, $additional_data);
	}
}
