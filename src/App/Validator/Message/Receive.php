<?php

/**
 * Ushahidi Message Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Message;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Usecase\Message\CreateMessageRepository;
use Ushahidi\App\DataSource\Message\Type as MessageType;
use Ushahidi\App\DataSource\Message\Direction as MessageDirection;
use Ushahidi\App\DataSource\Message\Status as MessageStatus;
use Ushahidi\App\DataSource\DataSourceManager;

class Receive extends Validator
{
    protected $repo;
    protected $default_error_source = 'message';

    public function __construct(CreateMessageRepository $repo)
    {
        $this->repo = $repo;
    }

    protected function getRules()
    {
        // @todo inject
        $sources = app('datasources');

        return [
            'direction' => [
                ['not_empty'],
                ['in_array', [':value', [MessageDirection::INCOMING]]],
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
                    MessageStatus::RECEIVED,
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
            ]
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
