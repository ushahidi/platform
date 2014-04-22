<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Main UI Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\UI\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

abstract class Ushahidi_Controller_Main extends Controller_Template {

	public $template = 'index';

	public function action_index()
	{
		// access the config service
		$config = service('config');

		$this->template->config = array(
			// these values can be modified in db
			'site'     => $config->get('site')->asArray(),
			'features' => $config->get('features')->asArray(),

			// these can only be set in config files
			'oauth'    => Kohana::$config->load('ushahidiui.oauth'),

			// these are set dynamically
			'baseurl'  => URL::base(TRUE, TRUE),
			'imagedir' => Media::uri('/images/'),
			'cssdir'   => Media::uri('/css/'),
			'jsdir'    => Media::uri('/js/'),
		);
	}

}