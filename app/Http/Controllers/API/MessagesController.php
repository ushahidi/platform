<?php

namespace Ushahidi\App\Http\Controllers\API;

use Ushahidi\App\Http\Controllers\RESTController;
use Ushahidi\Factory\UsecaseFactory;
use Ushahidi\Core\Entity\MessageRepository;
use Illuminate\Http\Request;

/**
 * Ushahidi API Messages Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class MessagesController extends RESTController {

    /**
     * @var Ushahidi\Factory\UsecaseFactory
     */
    protected $usecaseFactory;

    /**
     * @var Ushahidi\Core\Usecase
     */
    protected $usecase;

    public function __construct(UsecaseFactory $usecaseFactory, MessageRepository $messages) {
        parent::__construct($usecaseFactory);

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

        if ($message->post_id === NULL)
        {
            throw abort(404, 'Post does not exist for this message');
        }

        $this->usecase = $this->usecaseFactory
            ->get('posts', 'read')
            ->setIdentifiers([
                'id' => $message->post_id,
                'type' => 'report'
            ]);

        return $this->prepResponse($this->executeUsecase(), $request);
    }
}
