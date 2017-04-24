<?php

namespace Ushahidi\App\Http\Controllers\API;

use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Http\Request;

/**
 * Ushahidi API Media Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class MediaController extends RESTController
{

    protected function getResource()
    {
        return 'media';
    }

    /**
     * Create a media
     *
     * POST /api/media
     *
     * @return void
     */
    public function store(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'create')
            // Does not use `request->json()`, as uploads are not sent via the API,
            // but rather as a "normal" web request.
            // @todo use $request->all() since that pulls in both files and input
            //->setPayload($request->all());
            ->setPayload(array_merge($request->input(), $_FILES));

        return $this->prepResponse($this->executeUsecase(), $request);
    }
}
