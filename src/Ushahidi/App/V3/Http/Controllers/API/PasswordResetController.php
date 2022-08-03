<?php

namespace Ushahidi\App\V3\Http\Controllers\API;

use Illuminate\Http\Request;
use Ushahidi\App\V3\Http\Controllers\RESTController;

/**
 * Ushahidi API Tags Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class PasswordResetController extends RESTController
{
    protected function getResource()
    {
        return 'users';
    }

    public function store(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'getresettoken')
            ->setPayload($request->json()->all());

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    public function confirmOptions()
    {
        //$this->action_options_index_collection();
    }

    public function confirm(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'passwordreset')
            ->setPayload($request->json()->all());

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
