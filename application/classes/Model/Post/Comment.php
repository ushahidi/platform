<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Comments
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

class Model_Post_Comment extends ORM {
	/**
	 * A post has many [children] post_comments
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'children' => array(
			'model'  => 'Post_Comment',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * A post_comment belongs to a post and a user
	 * A post_comment also belongs to a [parent] post_comment
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'post' => array(),
		'user' => array(),

		'parent' => array(
			'model'  => 'post_comment',
			'foreign_key' => 'parent_id',
			),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => TRUE);

	/**
	 * Rules for the post_comment model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			
		);
	}
}