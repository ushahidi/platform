<?php

namespace Ushahidi\App\Http\Controllers\API\HXL;

use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ushahidi\App\Http\Controllers\RESTController;

/**
 * Demo HXL feature flag
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class HXLTagAttributesController extends RESTController
{

	protected function getResource()
	{
		return 'hxl_attribute_type_tag';
	}

	/**
	 * Retrieve a basic information about the API
	 *
	 * GET /api
	 *
	 * @return void
	 */
	public function index(Request $request)
	{
		/**
		 *
			{
			"form_attribute_types": ["string"], /// attribute types
			"tag_id": 0,
			"tag_name": "string",
			"tag_description": "string",
			"hxl_attributes": [
				{
				"description": "string",
				"name": "string",
				"id": 0
				}
			]
			}
			]
		 */
		$this->usecase = $this->usecaseFactory
			->get($this->getResource(), 'search');
		return $this->prepResponse($this->executeUsecase($request), $request);
	}
}
