<?php

/**
 * Ushahidi Post Values
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Entity;

class PostValue extends Entity
{
	public $id;
	public $post_id;
	public $form_attribute_id;
	public $value;
	public $created;

	public function getResource()
	{
		return 'post_values';
	}
}
