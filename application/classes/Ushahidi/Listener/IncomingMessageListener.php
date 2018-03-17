<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Incoming Message Listener
 *
 * Listens for incoming messages,
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Event\AbstractListener;
use League\Event\EventInterface;

class Ushahidi_Listener_IncomingMessageListener extends AbstractListener
{
    public function handle(EventInterface $event, $message = null)
    {
        //if part of a targeted survey, then determine if a new message needs to be sent out



    }
}
