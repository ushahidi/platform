<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Posts
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

class Model_Post extends ORM {
	/**
	 * A post has many comments decimal, geometry, int
	 * point, text, varchar, tasks
	 * 
	 * A post has and belongs to many sets and tags
	 * 
	 * A post has many [children] posts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'post_comments' => array(),
		'post_decimal' => array(),
		'post_geometry' => array(),
		'post_int' => array(),
		'post_point' => array(),
		'post_text' => array(),

		'tasks' => array(),

		'sets' => array('through' => 'posts_sets'),
		'tags' => array('through' => 'posts_tags'),

		'children' => array(
			'model'  => 'Post',
			'foreign_key' => 'parent_id',
			),
		);

	/**
	 * A post belongs to a user, and a [parent]
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'user' => array(),

		'parent' => array(
			'model'  => 'post',
			'foreign_key' => 'parent_id',
			),
		);


	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => TRUE);

	/**
	 * Rules for the post model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'form_id' => array(
				array('not_empty'),
				array('numeric'),
				array(array($this, 'form_exists'), array(':validation', ':field', ':value'))
			),

			'title' => array(
				array('not_empty'),
				array('max_length', array(':value', 150))
			),

			// Post Types
			'status' => array(
				array('in_array', array(':value', array(
					'draft',
					'publish',
					'pending'
				)) )
			),

			// Post Types
			'type' => array(
				array('not_empty'),
				array('in_array', array(':value', array(
					'report',
					'revision',
					'comment',
					'alert'
				)) )
			)
		);
	}

	/**
	 * Callback function to check if form exists
	 */
	public function form_exists($validation, $field, $value)
	{
		$form = ORM::factory('Form')
			->where('id', '=', $value)
			->find();

		if ( ! $form->loaded() )
		{
			$validation->error($field, 'form_exists');
		}
	}
}
