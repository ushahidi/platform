<?php

namespace Ushahidi\App\DataSource\Message;

/**
 * Interface for DataSource Message Status
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 *
 */
interface Status
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
