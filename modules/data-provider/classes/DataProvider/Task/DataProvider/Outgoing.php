<?php defined('SYSPATH') or die('No direct access allowed');
/**
 * Data Provider Outgoing Message Task
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Tasks
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

abstract class DataProvider_Task_DataProvider_Outgoing extends Minion_Task
{

	protected $_options = array(
		'provider' => FALSE,
		'limit' => 20,
	);

	/**
	 * Execute Task
	 *
	 * @return null
	 */
	protected function _execute(array $params)
	{
		$count = DataProvider::process_pending_messages($params['limit'], $params['provider']);
		echo __("Processed :count messages", array(':count' => $count))."\n";
	}

}