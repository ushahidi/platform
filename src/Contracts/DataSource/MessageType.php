<?php

namespace Ushahidi\Contracts\DataSource;

/**
 * Interface for DataSource Message Types
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 *
 */
interface MessageType
{
    const SMS = 'sms';
    const TWITTER = 'twitter';
    const IVR = 'ivr';
    const EMAIL = 'email';
}
