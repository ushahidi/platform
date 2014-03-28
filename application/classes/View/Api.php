<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Api View parent class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Views
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

abstract class View_Api {

	// View model
	protected $_model;
	// Current User model
	protected $_user;
	// ACL model
	protected $_acl;

	/**
	 * Returns a new View object.
	 *
	 *     $view = View::factory($name);
	 *
	 * @param   string  view name
	 * @param   array   view model
	 * @return  View
	 */
	public static function factory($name, array $model = NULL)
	{
		$class = 'View_Api_'.strtr($name, '/', '_');
		return new $class($model);
	}

	/**
	 * Sets the initial view model. Views should almost
	 * always only be created using [View::factory].
	 *
	 *     $view = new View($model);
	 *
	 * @param   array   model
	 * @return  void
	 */
	public function __construct(array $model = NULL)
	{
		if ($model !== NULL)
		{
			$this->_model = $model;
		}
	}

	/**
	 * Set model for this view
	 * @param Model $model
	 */
	public function set_model($model)
	{
		$this->_model = $model;
	}

	/**
	 * Set acl for this view
	 * @param Model $model
	 */
	public function set_acl($acl)
	{
		$this->_acl = $acl;
	}

	/**
	 * Set user for this view
	 * @param Model $model
	 */
	public function set_user($user)
	{
		$this->_user = $user;
	}

	/**
	 * Renders the view object to an array.
	 *
	 * @param    string  view filename
	 * @return   array
	 */
	abstract public function render($model = NULL);

}
