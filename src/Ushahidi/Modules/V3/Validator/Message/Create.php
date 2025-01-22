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
use Ushahidi\Contracts\Repository\Usecase\CreateMessageRepository;
use Ushahidi\Contracts\Repository\Entity\UserRepository;
use Ushahidi\DataSource\Contracts\MessageDirection;
use Ushahidi\DataSource\Contracts\MessageStatus;

class Create extends LegacyValidator
{
    protected $repo;
    protected $default_error_source = 'message';

    public function __construct(CreateMessageRepository $repo, UserRepository $user_repo)
    {
        $this->repo = $repo;
        $this->user_repo = $user_repo;
    }

    protected function getRules()
    {
        // @todo inject
        /** @var \Ushahidi\DataSource\DatasourceManager */
        $sources = app('datasources');

        return [
            'direction' => [
                ['not_empty'],
                ['in_array', [':value', [MessageDirection::OUTGOING]]],
            ],
            'message' => [
                ['not_empty'],
            ],
            'datetime' => [
                [[$this, 'validDate'], [':value']],
            ],
            'type' => [
                ['not_empty'],
                ['max_length', [':value', 255]],
                // @todo this should be shared via repo or other means
                ['in_array', [':value', ['sms', 'ivr', 'email', 'twitter']]],
            ],
            'data_source' => [
                ['in_array', [':value', array_keys($sources->getEnabledSources())]],
            ],
            'data_source_message_id' => [
                ['max_length', [':value', 511]],
            ],
            'status' => [
                ['not_empty'],
                ['in_array', [':value', [
                    // @todo this should be shared via repo
                    MessageStatus::PENDING,
                ]]],
            ],
            'parent_id' => [
                ['numeric'],
                [[$this->repo, 'parentExists'], [':value']],
            ],
            'post_id' => [
                ['numeric'],
            ],
            'contact_id' => [
                ['numeric'],
            ],
            'user_id' => [
                [[$this->user_repo, 'exists'], [':value']]
            ],
        ];
    }

    public function validDate($str)
    {
        if ($str instanceof \DateTimeInterface) {
            return true;
        }
        return (strtotime($str) !== false);
    }
}
