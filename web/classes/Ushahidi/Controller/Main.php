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
		// access the config repository
		$config = service('repository.config');

		$this->template->config = array(
			// these values can be modified in db
			'site'     => $config->get('site')->asArray(),
			'features' => $config->get('features')->asArray(),
			'map'      => $config->get('map')->asArray(),

			// these can only be set in config files
			'oauth'    => Kohana::$config->load('ushahidiui.oauth'),

			// these are set dynamically
			// 'baseurl' is used for oauth2 redirecturi so must be an absolute URL including domain
			'baseurl'  => URL::base(TRUE, TRUE),
			// 'basepath' is used for Backbone.History root so shouldn't include domain, just path and index.php
			'basepath'  => URL::base(FALSE, TRUE),
			// Other urls are expected to include the baseurl too
			'apiurl'   => URL::base(FALSE, TRUE) . 'api/v2/',
			'imagedir' => Media::url('/images/'),
			'cssdir'   => Media::url('/css/'),
			'jsdir'    => Media::url('/js/'),
		);
	}

}
