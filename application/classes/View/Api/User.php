<?php defined('SYSPATH') or die('No direct script access.');

/**
 * API User View
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Views
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class View_Api_User extends View_Api {

	// View model
	protected $_model;

	/**
	 * Renders the view object to a string. Global and local data are merged
	 * and extracted to create local variables within the view file.
	 *
	 *     $output = View::render();
	 *
	 * [!!] Global variables with the same key name as local variables will be
	 * overwritten by the local variable.
	 *
	 * @param    string  view filename
	 * @return   string
	 * @throws   Kohana_View_Exception
	 * @uses     View::capture
	 */
	public function render($model = NULL)
	{
		if ($model == NULL)
		{
			$model = $this->model;
		}

		$response = array();
		if ( $model->loaded() )
		{
			$response = array(
				'id' => $model->id,
				'url' => URL::site('api/v'.Ushahidi_Api::version().'/users/'.$model->id, Request::current()),
				'username' => $model->username,
				'realname' => $model->realname,
				'role' => $model->role,
				'gravatar' => md5($model->email),
			);

			// Return full user details if user has permission
			if ($this->_acl->is_allowed($this->_user, $model, 'get_full') )
			{
				$response['email'] = $model->email;
				$response['logins'] = $model->logins;
				$response['last_login'] = $model->last_login;
				$response['failed_attempts'] = $model->failed_attempts;
				$response['last_attempt'] = $model->last_attempt;

				$response['created'] = ($created = DateTime::createFromFormat('U', $model->created))
					? $created->format(DateTime::W3C)
					: $model->created;
				$response['updated'] = ($updated = DateTime::createFromFormat('U', $model->updated))
					? $updated->format(DateTime::W3C)
					: $model->updated;
			}

		}

		return $response;
	}

}
