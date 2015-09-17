<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Client Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Client;

class Ushahidi_Repository_Client extends Ushahidi_Repository
{

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'oauth_clients';
	}

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		return new Client($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['q', 'name', /* LIKE name */];
	}

}
