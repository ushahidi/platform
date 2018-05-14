<?php
/**
 * Ushahidi API Post Lock Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Http\Controllers\API\Posts;

use Illuminate\Http\Request;

class LockController extends PostsController
{

    // Ushahidi_Rest
    protected function getResource()
    {
        return 'posts_lock';
    }

    // Get Lock
    public function store(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'create')
            ->setPayload($this->getPayload($request))
            ->setIdentifiers($this->getIdentifiers($request))
            ->setFormatter(service("formatter.entity.post.lock"));

        return $this->prepResponse($this->executeUsecase($request), $request);
    }

    // Break Lock
    public function destroy(Request $request)
    {
        $this->usecase = $this->usecaseFactory
            ->get($this->getResource(), 'delete')
            ->setIdentifiers($this->getIdentifiers($request))
            ->setFormatter(service("formatter.entity.post.lock"));

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
