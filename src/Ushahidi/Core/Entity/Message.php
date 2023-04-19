<?php

/**
 * Ushahidi Message
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2023 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Entity;

interface Message extends Entity
{
    // Valid boxes are defined as constants.
    const INBOX = 'inbox';

    const OUTBOX = 'sent';

    // Valid directions are defined as constants.
    const INCOMING = 'incoming';

    const OUTGOING = 'outgoing';

    // Valid status types are defined as constants.
    const PENDING = 'pending';

    const RECEIVED = 'received';

    const EXPIRED = 'expired';

    const CANCELLED = 'cancelled';

    const FAILED = 'failed';
}
