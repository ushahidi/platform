<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Sets
 * 
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Set extends ORM {
	/**
	 * A set has and belongs to many posts
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'posts' => array('through' => 'posts_sets'),
		);

	/**
	 * A set belongs to a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'user' => array()
		);
}
