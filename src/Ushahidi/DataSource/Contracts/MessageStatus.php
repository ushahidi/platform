<?php

namespace Ushahidi\DataSource\Contracts;

/**
 * Interface for DataSource Message Status
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Contracts
 * @copyright  2022 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 *
 */
interface MessageStatus
{
    // Waiting to be sent
    const PENDING        = 'pending';
    const SENT           = 'sent';
    const RECEIVED       = 'received';
    const EXPIRED        = 'expired';
    const CANCELLED      = 'cancelled';
    const FAILED         = 'failed';
    const UNKNOWN        = 'unknown';
    const DEFAULT_STATUS = 'pending';
}
