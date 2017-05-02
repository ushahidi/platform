<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Form Role
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_Form_Role extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;

	public function __invoke($entity)
	{
		$data = [
			'id'  => $entity->id,
			'url' => url('forms/' . $entity->form_id . '/roles/' . $entity->id),
			'form_id' => $entity->form_id,
			'role_id' => $entity->role_id,
			];

		$data = $this->add_metadata($data, $entity);

		return $data;
	}
}
