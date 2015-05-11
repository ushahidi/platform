<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Map Config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Config
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

return array(
	// Enable marker clustering with leaflet.markercluster
	'clustering'     => FALSE,
	'cluster_radius' => 50,
	// Map start location
	'default_view' => array(
		'lat'                => -1.3048035,
		'lon'                => 36.8473969,
		'zoom'               => 2,
		'baselayer'          => 'MapQuest',
		'fit_map_boundaries' => true, // Fit map boundaries to current data rendered
		'icon'               => 'map-marker', // Fontawesome Markers
		'color'              => 'blue'
	)
);
