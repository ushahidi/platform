<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\GetValuesForPostRepository;

class Ushahidi_Repository_PostValueProxy implements GetValuesForPostRepository
{
	protected $factory;

	public function __construct(Ushahidi_Repository_PostValueFactory $factory)
	{
		$this->factory = $factory;
	}

	// GetValuesForPostRepository
	public function getAllForPost($post_id)
	{
		$results = [];

		$this->factory->each(function ($repo) use ($post_id, &$results) {
			$results = array_merge($results, $repo->getAllForPost($post_id));
		});

		return $results;
	}

}
