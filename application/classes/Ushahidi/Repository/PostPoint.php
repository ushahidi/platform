<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Point Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\PostValue;
use Ushahidi\Entity\PostValueRepository;

class Ushahidi_Repository_PostPoint extends Ushahidi_Repository_PostGeometry
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'post_point';
	}

}
