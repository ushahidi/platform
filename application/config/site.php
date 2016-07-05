<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Kohana Site Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

/**
 * Site settings
 *
 * The following options are available:
 *
 * - string   name          Display name of the site.
 * - string   description   A brief description of the site.
 * - string   email         Site contact email.
 * - string   timezone      Default timezone for the site. See http://php.net/manual/en/timezones.php
 * - string   language      Native language for the site in ISO 639-1 format. See http://en.wikipedia.org/wiki/ISO_639-1
 * - string   date_format   Set format in which to return dates. See http://php.net/manual/en/datetime.createfromformat.php
 */

$clientUrl = getenv('CLIENT_URL');
if (!empty(getenv("MULTISITE_DOMAIN"))) {
	try {
		$host = \League\Url\Url::createFromServer($_SERVER)->getHost()->toUnicode();
		$clientUrl = str_replace(getenv("MULTISITE_DOMAIN"), getenv("MULTISITE_CLIENT_DOMAIN"), $host);
	} catch (Exception $e) {

	}
}

return array(
	'name'        => '',
	'description' => '',
	'email'       => '',
	'timezone'    => 'UTC',
	'language'    => 'en-US',
	'date_format' => 'n/j/Y',
	'client_url'  => $clientUrl ?: false,
	'first_login' => true,
	'tier'        => 'free',
	'private'     => false,
);
