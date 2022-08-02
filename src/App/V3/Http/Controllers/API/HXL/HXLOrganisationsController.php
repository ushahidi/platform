<?php

namespace Ushahidi\App\V3\Http\Controllers\API\HXL;

use Illuminate\Http\Request;
use Ushahidi\App\V3\Http\Controllers\Controller;
use Ushahidi\App\V3\Http\Controllers\RESTController;

/**
 * HXL Tags controller
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class HXLOrganisationsController extends RESTController
{
    protected function getResource()
    {
        return 'hxl_organisations';
    }

    /**
     * Retrieve All Entities
     *
     * GET /api/foo
     *
     * @return void
     */
    public function index(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'search');

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
