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

		// config results will be formatted as a hash
		$hash = service('config.format.hash');

		$this->template->config = array(
			// these values can be modified in db
			'site'     => $hash($config->all('site')),
			'features' => $hash($config->all('features')),

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