<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Formatter for Post Bulk Actions
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Ushahidi_Formatter_Post_Bulk extends Ushahidi_Formatter_API
{
    use FormatterAuthorizerMetadata;

	public function __invoke($response)
	{
		$data = [
			'count'  => $response[0],
			'actions' => $response[1],
			];

		return $data;
	}

}
