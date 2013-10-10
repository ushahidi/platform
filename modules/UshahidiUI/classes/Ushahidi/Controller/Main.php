<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Ushahidi_Controller_Main extends Controller_Template {
	
	public $template = 'index';
	
	public function action_index()
	{
		$this->template->site = array();
		$this->template->site['baseurl'] = Kohana::$base_url;
		$this->template->site['imagedir'] = Media::uri('/images/');
		$this->template->site['cssdir'] = Media::uri('/css/');
		$this->template->site['jsdir'] = Media::uri('/js/');
		$this->template->site['oauth'] = Kohana::$config->load('ushahidiui.oauth');
		
	}
	
}