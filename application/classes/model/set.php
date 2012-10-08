<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Sets
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

class Model_Set extends ORM
{
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
