<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Contacts
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_Contact extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;
}
