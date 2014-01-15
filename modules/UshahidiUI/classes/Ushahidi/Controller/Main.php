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
		$this->template->site = array();
		$this->template->site['baseurl'] = URL::base(TRUE, TRUE);
		$this->template->site['imagedir'] = Media::url('/images/');
		$this->template->site['cssdir'] = Media::url('/css/');
		$this->template->site['jsdir'] = Media::url('/js/');
		$this->template->site['oauth'] = Kohana::$config->load('ushahidiui.oauth');
		$this->template->site['site'] = Kohana::$config->load('site');

	}

}