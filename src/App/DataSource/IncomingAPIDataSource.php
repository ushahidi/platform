<?php

namespace Ushahidi\App\DataSource;

/**
 * Data Source interface for fetching messages via an API
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
interface IncomingAPIDataSource extends DataSource
{

    /**
     * Fetch messages from provider
     *
     * For services where we have to poll for message (Twitter, Email, FrontlineSMS) this should
     * poll the service and return an array of messages
     *
     * @param  boolean $limit   maximum number of messages to fetch at a time
     * @return array            array of messages
     */
    public function fetch($limit = false);
}
