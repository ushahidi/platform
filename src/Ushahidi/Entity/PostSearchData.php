<?php

/**
 * Ushahidi Platform Post Search Data
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Entity;

use Ushahidi\Data;

class PostSearchData extends Data
{
	public $q; // LIKE title OR content
	public $slug;
	public $parent;
	public $form;
	public $user;
	public $locale;
	public $status;
	public $created_after;
	public $created_before;
	public $updated_after;
	public $updated_before;
	public $bbox;
	public $tags;
	public $values;
	public $set;
}
