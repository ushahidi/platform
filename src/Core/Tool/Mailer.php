<?php

/**
 * Ushahidi Platform Mailer Tool
 *
 * Send emails
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface Mailer
{
	/**
	 * Send a templated email
	 *
	 * @param  string     $to     Destination email
	 * @param  string     $type   Email type (ie. template to use)
	 * @param  Array|null $params Params for populating the template
	 * @return void
	 */
	public function send($to, $type, Array $params = null);
}
