<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tag Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\RoleRepository;
use Ushahidi\Core\Usecase\Tag\UpdateTagRepository;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Tag_Update extends Validator
{
	protected $repo;
	protected $role;

	protected $default_error_source = 'tag';

	public function __construct(UpdateTagRepository $repo, RoleRepository $role)
	{
		$this->repo = $repo;
		$this->role = $role;
	}

	protected function getRules()
	{
		return [
			'tag' => [
				['min_length', [':value', 2]],
				// alphas, numbers, punctuation, and spaces
				['regex', [':value', '/^[\pL\pN\pP ]++$/uD']],
			],
			'parent_id' => [
				[[$this->repo, 'doesTagExist'], [':value']],
			],
			'slug' => [
				['min_length', [':value', 2]],
				['alpha_dash'],
				[[$this->repo, 'isSlugAvailable'], [':value']],
			],
			'description' => [
				// alphas, numbers, punctuation, and spaces
				['regex', [':value', '/^[\pL\pN\pP ]++$/uD']],
			],
			'type' => [
				['in_array', [':value', ['category', 'status']]],
			],
			'color' => [
				['color'],
			],
			'icon' => [
				['alpha_dash'],
			],
			'priority' => [
				['digit'],
			],
			'role' => [
				[[$this->role, 'doRolesExist'], [':value']],
			]
		];
	}
}
