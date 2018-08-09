<?php

/**
 * Ushahidi Form Contact
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class FormStats extends StaticEntity
{
    protected $total_responses;
    protected $total_recipients;
    protected $total_messages_sent;
    protected $total_messages_pending;
    // DataTransformer
    protected function getDefinition()
    {
        return [
            'total_responses'            => 'int',
            'total_recipients'       => 'int',
            'total_messages_sent'       => 'int',
            'total_messages_pending'       => 'int',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'form_stats';
    }
}
