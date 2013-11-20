<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Abstract controller class for automatic templating.
 *
 * @package    Kohana
 * @category   Controller
 * @author     Kohana Team
 * @copyright  (c) 2008-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 */
abstract class Controller_Layout extends Controller {

	/**
	 * @var  View  page template
	 */
	public $template = 'template';

	/**
	 * @var  View  page layout template
	 */
	public $layout = 'layout';

	/**
	 * @var  View  page header template
	 */
	public $header = 'header';

	/**
	 * @var  View  page footer template
	 */
	public $footer = 'footer';

	/**
	 * @var  boolean  auto render template
	 **/
	public $auto_render = TRUE;

	/**
	 * Loads the template [View] object.
	 */
	public function before()
	{
		//parent::before();

		if ($this->auto_render === TRUE)
		{
			// Load the template
			$this->template = View::factory($this->template);
			$this->header = View::factory($this->header);
			$this->footer = View::factory($this->footer);

			$this->layout = View::factory($this->layout)
				->bind('content', $this->template)
				->bind('header', $this->header)
				->bind('footer', $this->footer);
		}
	}

	/**
	 * Assigns the template [View] as the request response.
	 */
	public function after()
	{
		if ($this->auto_render === TRUE)
		{
			$this->response->body($this->layout->render());
		}

		//parent::after();
	}

}
