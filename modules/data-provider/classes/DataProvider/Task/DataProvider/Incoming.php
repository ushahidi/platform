<?php defined('SYSPATH') or die('No direct access allowed');
/**
 * Data Provider Incoming Message Task
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Tasks
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

abstract class DataProvider_Task_DataProvider_Incoming extends Minion_Task
{

	protected $_options = array(
		'provider' => FALSE,
		'limit' => FALSE,
	);

	/**
	 * Execute Task
	 *
	 * @return null
	 */
	protected function _execute(array $params)
	{
		if ($params['provider'])
		{
			$providers = array($params['provider']);
		}
		else
		{
			$providers = DataProvider::get_enabled_providers();
		}

		foreach ($providers as $provider_name)
		{
			$provider = DataProvider::factory($provider_name);

			// Get messages
			$count = $provider->fetch($params['limit']);
			echo __("Fetched :count messages from :provider", array(':count' => $count, ':provider' => $provider_name))."\n";
		}
	}

}