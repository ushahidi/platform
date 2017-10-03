<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Console Formatter
 *
 * Takes an entity object and returns an array.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Exception\FormatterException;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_Post_Lock extends Ushahidi_Formatter_API
{
	use FormatterAuthorizerMetadata;
}