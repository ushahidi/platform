<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Media Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

//use Ushahidi\Core\Entity\PostValue;
//use Ushahidi\Core\Entity\PostValueRepository;

class Ushahidi_Repository_Post_Media extends Ushahidi_Repository_Post_Value
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'post_media';
	}
}
