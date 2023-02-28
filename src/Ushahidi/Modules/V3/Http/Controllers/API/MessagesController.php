<?php

namespace Ushahidi\Modules\V3\Http\Controllers\API;

use Illuminate\Http\Request;
use Ushahidi\Core\Entity\MessageRepository;
use Ushahidi\Modules\V3\Factory\UsecaseFactory;
use Ushahidi\Modules\V3\Http\Controllers\RESTController;
use Ushahidi\Multisite\MultisiteManager;

/**
 * Ushahidi API Messages Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class MessagesController extends RESTController
{
    /**
     * @var \Ushahidi\Modules\V3\Factory\UsecaseFactory
     */
    protected $usecaseFactory;

    /**
     * @var \Ushahidi\Contracts\Usecase
     */
    protected $usecase;

    public function __construct(
        UsecaseFactory $usecaseFactory,
        MultisiteManager $multisite,
        MessageRepository $messages
    ) {
        parent::__construct($usecaseFactory, $multisite);

        $this->messages = $messages;
    }

    protected function getResource()
    {
        return 'messages';
    }

    /**
     * GET post created from message
     *
     * GET /messages/:id/post
     */
    public function showPost(Request $request, $id)
    {
        // @todo make this a proper use case
        $message = $this->messages->get($id);

        if ($message->post_id === null) {
            abort(404, 'Post does not exist for this message');
        }

        $this->usecase = $this->usecaseFactory
            ->get('posts', 'read')
            ->setIdentifiers([
                'id' => $message->post_id,
                'type' => 'report',
            ]);

        return $this->prepResponse($this->executeUsecase($request), $request);
    }
}
