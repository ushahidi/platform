<?php

namespace Ushahidi\DataSource\Contracts;

/**
 * Interface for DataSource Message Direction
 *
 * @author     Ushahidi Dev Team, Emmanuel Kala <emkala(at)gmail.com>
 * @package    Ushahidi - http://ping.ushahidi.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 *
 */
interface MessageDirection
{
    const INCOMING = 'incoming';

    const OUTGOING = 'outgoing';
}
