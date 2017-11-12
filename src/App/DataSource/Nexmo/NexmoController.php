<?php

namespace Ushahidi\App\DataSource\Nexmo;

/**
 * Base class for all Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\DataSource
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\App\DataSource\DataSourceController;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NexmoController extends DataSourceController
{

    protected $source = 'nexmo';

	public function handleRequest(Request $request)
    {
		require_once __DIR__ . '/vendor/nexmo/NexmoMessage.php';

		// Pong Sender
		$ipAddress = $request->getClientIp();
		$continue = false;
		foreach ($this->subnets as $subnet) {
			if (($this->ipInRange($ipAddress, $subnet))) {
				return abort(403, 'IP Address not in allowed range');
			};
		}

		$sms = new \NexmoMessage(false, false); // Params don't matter for this

		if (! $sms->inboundText()) {
			throw abort(400, "Invalid message");
		}

		// Remove Non-Numeric characters because that's what the DB has
		$to = preg_replace("/[^0-9,.]/", "", $sms->to);
		$from  = preg_replace("/[^0-9,.]/", "", $sms->from);

		$this->save([
			'type' => MessageType::SMS,
			'from' => $from,
			'contact_type' => Contact::PHONE,
			'message' => $sms->text,
			'to' => $to,
			'title' => null,
			'data_provider_message_id' => $sms->message_id,
			'data_provider' => 'nexmo'
		]);

		// Then return success
		return [
			'success' => true
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

			app('log')->error(
				'IP Address Range argument is not in 1.2.3.4/24 or 1.2.3.4/255.255.255.0 format'
			);

			return false;
		}
	}
}
