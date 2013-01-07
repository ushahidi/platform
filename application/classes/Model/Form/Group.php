<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Form_Groups
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

class Model_Form_Group extends ORM {
	/**
	 * A form_group has many groups
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'form_attributes' => array(),
		);

	/**
	 * A form_group belongs to a form
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'form' => array(),
		);
}