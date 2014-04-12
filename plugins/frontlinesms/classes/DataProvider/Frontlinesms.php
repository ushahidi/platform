<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * FrontlineSms Data Providers
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\FrontlineSms
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class DataProvider_FrontlineSms extends DataProvider {

	/**
	 * Contact type user for this provider
	 */
	public $contact_type = Model_Contact::PHONE;

	/**
	 * Sets the FROM parameter for the provider
	 *
	 * @return int
	 */
	public function from()
	{
		// Get provider phone (FROM)
		// Replace non-numeric
		$this->_from = preg_replace("/[^0-9,.]/", "", parent::from());

		return $this->_from;
	}

}