<?php

/**
 * Ushahidi Platform Signer Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface Signer
{

	public function sign($fullUrl, $data);

	public function computeSignature($url, $data = array());

	public function validate($expectedSignature, $url, $data = array());

	public static function compare($a, $b);
}
