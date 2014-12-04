<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity\GetValuesForPostRepository;

class Ushahidi_Repository_PostValueProxy implements GetValuesForPostRepository
{
	protected $factory;
	protected $include_types;

	public function __construct(Ushahidi_Repository_PostValueFactory $factory, Array $include_types = [])
	{
		$this->factory = $factory;
		$this->include_types = $include_types;
	}

	// GetValuesForPostRepository
	public function getAllForPost($post_id, Array $include_attributes = [])
	{
		$results = [];

		$this->factory->each(function ($repo) use ($post_id, $include_attributes, &$results) {
			$results = array_merge($results, $repo->getAllForPost($post_id, $include_attributes));
		}, $this->include_types);

		return $results;
	}

}
