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
use Ushahidi\DataSource\Contracts\MessageStatus;
use Ushahidi\DataSource\Contracts\MessageDirection;
use Ushahidi\Contracts\Repository\Usecase\CreateMessageRepository;

class Receive extends LegacyValidator
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
                [[$this, 'notEmptyIfTwitter'], [':value',':validation']],
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
    public function notEmptyIfTwitter($value, $validation)
    {
        if ($this->validation_engine->getFullData('data_source') === 'twitter') {
            return true;
        }
        if (empty($value)) {
            $validation->error('message', 'not_empty');
            return false;
        }
        return true;
    }
    public function validDate($str)
    {
        if ($str instanceof \DateTimeInterface) {
            return true;
        }
        return (strtotime($str) !== false);
    }
}
