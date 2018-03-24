<?php

/**
 * Ushahidi Media Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class PostValueMedia extends StaticEntity
{
	protected $id;
	protected $user_id;
	protected $caption;
	protected $created;
	protected $updated;
	protected $mime;
	protected $o_filename;
	protected $o_size;
	protected $o_width;
	protected $o_height;
	protected $value;
	protected $key;
	// DataTransformer
	public function getDefinition()
	{
		return [
			'id'         => 'int',
			'user_id'    => 'int',
			'caption'    => 'string',
			'created'    => 'int',
			'updated'    => 'int',
			'mime'       => 'string',
			'o_filename' => 'string',
			'o_size'     => 'int',
			'o_width'    => 'int',
			'o_height'   => 'int',
			'value'      => null, // needed for csv values,
			'key'		=> 'string'
		];
	}

	// Entity
	public function getResource()
	{
		return 'media';
	}
}
