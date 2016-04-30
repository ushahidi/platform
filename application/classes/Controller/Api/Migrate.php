<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Tags Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Migrate extends Ushahidi_Rest {

	protected $_action_map = array
	(
		Http_Request::GET    => 'get',
		Http_Request::OPTIONS => 'options'
	);

	protected function _scope()
	{
		return 'config';
	}

	public function action_get_migrate()
	{
		$path  = 'classes/Controller/Api';
		$files = Arr::flatten(Kohana::list_files($path));
		$trim  = strlen($path);

		$endpoints = [];
		foreach ($files as $file => $path)
		{
			if (__FILE__ === $path)
			{
				continue; // skip the index
			}

			// remove the base path (up to Api/) and the .php extension
			$file = substr($file, $trim + 1, -4);

			// @todo this would be much more awesome if it gave back a URI
			$endpoints[] = strtolower($file);
		}
		sort($endpoints);

		$user = service('session.user');

		$this->_response_payload = [
			'now'       => date(DateTime::W3C),
			'version'   => static::$version,
			'endpoints' => $endpoints,
			'user'      => [
				'id'       => $user->id,
				'email'    => $user->email,
				'realname' => $user->realname,
			],
		];
	}
}
