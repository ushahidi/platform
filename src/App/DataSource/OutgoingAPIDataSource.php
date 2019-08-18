<?php

namespace Ushahidi\App\DataSource;

/**
 * Data Source interface for sending messages via an API
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
interface OutgoingAPIDataSource extends DataSource
{

    /**
     * @param  string  to Phone number to receive the message
     * @param  string  message Message to be sent
     * @param  string  title   Message title
     * @return array   Array of message status, and tracking ID.
     */
    public function send($to, $message, $title = "");
}
