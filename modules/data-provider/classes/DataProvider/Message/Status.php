<?php defined('SYSPATH') or die('No direct access allowed');

/**
 * Interface for DataProvider Message Status
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 *
 */
interface DataProvider_Message_Status {

	const PENDING = 'pending';
	const ARCHIVED = 'archived';
	const RECEIVED = 'received';
	const EXPIRED = 'expired';
	const CANCELLED = 'cancelled';
	const FAILED = 'failed';
	const UNKNOWN = 'unknown';
	const SENT = 'sent';

}