<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Search Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Tool\Parser;
use Ushahidi\Entity\PostSearchData;

class Ushahidi_Parser_Post_Search implements Parser
{

	public function __invoke(Array $data)
	{
		// Filter value search params to only true-ish values
		if (!empty($data['values']))
		{
			$data['values'] = array_filter($data['values']);
		}

		// Parse status params
		if (empty($data['status']))
		{
			$data['status'] = 'published';
		}
		elseif ($data['status'] == 'all')
		{
			unset($data['status']);
		}

		// Parse tags param
		if (isset($data['tags']))
		{
			// Default to filtering to ANY of the tags.
			// if tags isn't an array or doesn't have any/all keys set
			if (! is_array($data['tags']) OR (! isset($data['tags']['any']) AND ! isset($data['tags']['all'])))
			{
				$data['tags'] = array('any' => $data['tags']);
			}

			if (isset($data['tags']['any']) AND ! is_array($data['tags']['any']))
			{
				$data['tags']['any'] = explode(',', $data['tags']['any']);
			}

			if (isset($data['tags']['all']) AND ! is_array($data['tags']['all']))
			{
				$data['tags']['all'] = explode(',', $data['tags']['all']);
			}
		}

		return new PostSearchData($data);
	}
}
