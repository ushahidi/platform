<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Tag
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity;
use Ushahidi\Tool\Authorizer;

class Ushahidi_Formatter_Tag extends Ushahidi_Formatter_API
{
	protected $auth;

	public function __construct(Authorizer $auth)
	{
		$this->auth = $auth;
	}

	protected function add_metadata(Array $data, Entity $tag)
	{
		return $data + [
			// Add the allowed HTTP methods (called privileges internally)
			'allowed_methods' => $this->auth->getAllowedPrivs($tag),
		];
	}

	protected function format_color($value)
	{
		// enforce a leading hash on color, or null if unset
		$value = ltrim($value, '#');
		return $value ? '#' . $value : null;
	}
}
