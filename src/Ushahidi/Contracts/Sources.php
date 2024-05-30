<?php

namespace Ushahidi\Contracts;

/**
 * Interface for post sources
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 *
 */
interface Sources
{
    const WEB = 'web';
    const MOBILE = 'mobile';
    const SMS = 'sms';
    const TWITTER = 'twitter';
    const WHATSAPP = 'whatsapp';
    const EMAIL = 'email';
    const USSD = 'ussd';
}
