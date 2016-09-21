<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Create Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\FormStageRepository;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\RoleRepository;
use Ushahidi\Core\Entity\PostSearchData;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Traits\PermissionAccess;
use Ushahidi\Core\Traits\AdminAccess;
use Ushahidi\Core\Traits\Permissions\ManagePosts;
use Ushahidi\Core\Usecase\Post\UpdatePostRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostTagRepository;

class Ushahidi_Validator_Post_Import extends Ushahidi_Validator_Post_Create
{
  protected function getRules()
	{
		return array_merge(parent::getRules(), [
      'values' => [
				[[$this, 'checkValues'], [':validation', ':value', ':fulldata']]
		  ],
      'completed_stages' => [
				[[$this, 'checkStageInForm'], [':validation', ':value', ':fulldata']]
      ]
  ]);
	}
}
