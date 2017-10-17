<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for PostsChangelog
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Exception\FormatterException;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;
use Ushahidi\Core\Entity\UserRepository;

class Ushahidi_Formatter_Post_Changelog extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;

	protected $user_repo;

	public function setUserRepo(UserRepository $user_repo)
	{
		$this->user_repo = $user_repo;
	}

	/**
	 * Format relations into url/id arrays
	 * @param  string $resource resource name as used in urls
	 * @param  int    $id       resource id
	 * @return array
	 */
	protected function get_relation($resource, $id)
	{

		if ($resource == 'users')
		{
			$user_obj = $this->user_repo->get(['id'=> $id ])->asArray();
			return !$id ? NULL : [
				'id'  => intval($id),
				'realname'  => $user_obj['realname'],
				'url' => URL::site(Ushahidi_Rest::url($resource, $id), Request::current()),
			];
		}

		return !$id ? NULL : [
			'id'  => intval($id),
			'url' => URL::site(Ushahidi_Rest::url($resource, $id), Request::current()),
		];
	}


}
