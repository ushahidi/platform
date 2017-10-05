<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Lock Update Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Post_Lock_Update extends Validator
{
	protected $post_repo;

	protected $default_error_source = 'post_lock';

	public function __construct(PostRepository $post_repo)
	{
		$this->post_repo = $post_repo;
	}

	protected function getRules()
	{
		return [
			'post_id' => [
				[[$this->post_repo, 'exists'], [':value']],
			],
		];
	}
}
