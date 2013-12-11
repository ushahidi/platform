<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Twilio SMS Response Template
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataProvider\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<Response>
<Message><?php echo $response; ?></Message>
</Response>