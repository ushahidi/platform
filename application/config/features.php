<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Features Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return array(
	// determines which features are available in a deployment - in contrast
	// to which features are enabled or not - i.e. a feature can only ever be
	// enabled if it is also available, but may be available, but not enabled

	// post data views
	'post_view_map' => TRUE,
	'post_view_list' => TRUE,
	'post_view_chart' => FALSE,
	'post_view_timeline' => FALSE,
	// available data sources
	'smssync' => FALSE,
	'frontlinesms' => FALSE,
	'email' => FALSE,
	'twilio' => FALSE,
	'nexmo' => FALSE,
	'twitter' => FALSE,
);
