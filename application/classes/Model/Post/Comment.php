<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Comments
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Post_Comment extends ORM {
	/**
	 * A post has many [children] post_comments
	 *
	 * @var array Relationships
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
	 * @var array Relationships
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