<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for PostsChangelog
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Exception\FormatterException;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_Post_Changelog extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;

	/**
	 * Method that can add any kind of additional metadata about the entity,
	 * by overloading this method in an extended class.
	 *
	 * Must return the formatted data!
	 *
	 * @param  Array  $data   formatted data
	 * @param  Entity $entity resource
	 * @return Array
	 */
	protected function add_metadata(Array $data, Entity $entity)
	{
		//extending the given data with hydrated values to be included in the GET changelog data
		return $data;
	}

	protected function hydrateUserData()
	{

	}

	/**
	 * Format relations into url/id arrays
	 * @param  string $resource resource name as used in urls
	 * @param  int    $id       resource id
	 * @return array
	 */
	protected function get_relation($resource, $id)
	{
		Kohana::$log->add(Log::INFO, 'Getting relation for id:'.print_r($id, true).' of resource: '.print_r($resource, true));

		return !$id ? NULL : [
			'id'  => intval($id),
			'url' => URL::site(Ushahidi_Rest::url($resource, $id), Request::current()),
		];
	}


}
