<?php

namespace Ushahidi\Modules\V3\Http\Controllers\API\Users;

use Illuminate\Http\Request;
use Ushahidi\Modules\V3\Http\Controllers\RESTController;

/**
 * Ushahidi API Users Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class UsersController extends RESTController
{
    protected function getResource()
    {
        return 'users';
    }

    /**
     * Get current user
     *
     * GET /api/users/me
     *
     * @return void
     */
    public function showMe(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'read')
            ->setIdentifiers([
                'id' => $request->user()->id ?: 0,
            ]);

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    /**
     * Get options for /users/me
     *
     * @return void
     */
    public function optionsMe()
    {
        $this->response->status(200);
    }

    /**
     * Update current user
     *
     * PUT /api/users/me
     *
     * @return void
     */
    public function updateMe(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'update')
            ->setIdentifiers([
                'id' => $request->user()->id ?: 0,
            ])
            ->setPayload($request->json()->all());

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
