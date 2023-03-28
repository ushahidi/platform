<?php

/**
 * Ushahidi Message Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Message;

use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Core\Entity\MessageRepository;

class Update extends LegacyValidator
{
    protected $repo;
    protected $default_error_source = 'message';

    public function __construct(MessageRepository $repo)
    {
        $this->repo = $repo;
    }

    protected function getRules()
    {
        return [
            'status' => [
                [[$this->repo, 'checkStatus'], [':value', $this->validation_engine->getFullData('direction')]]
            ]
        ];
    }
}
