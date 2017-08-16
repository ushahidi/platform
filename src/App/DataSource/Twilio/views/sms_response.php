<?php
/**
 * Twilio SMS Response Template
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource\Twilio
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<Response>
<Message><?php echo $response; ?></Message>
</Response>
