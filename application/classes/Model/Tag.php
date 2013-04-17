<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Tags
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Tag extends ORM {
	/**
	 * A tag has and belongs to many posts
	 * A tag has many [children] tags
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'posts' => array('through' => 'posts_tags'),

		'children' => array(
			'model' => 'Tag',
			'foreign_key' => 'parent_id'
			)
		);

	/**
	 * A tag belongs to a [parent] user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'parent' => array(
			'model'  => 'Tag',
			'foreign_key' => 'parent_id',
			),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
}
