<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Contact Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\Contact;
use Ushahidi\Entity\ContactRepository;

class Ushahidi_Repository_Contact implements ContactRepository
{

	const TABLE = 'contacts';

	public function get($id)
	{
		$query = DB::select('*')
			->from(self::TABLE)
			->where('id', '=', $id)
			;
		$result = $query->execute();
		return new Contact($result->current());
	}

	public function add(Contact $contact)
	{
		$data = array_filter($contact->asArray());
		unset($data['id']); // always autoinc
		$query = DB::insert(self::TABLE)
			->columns(array_keys($data))
			->values(array_values($data))
			;
		list($contact->id, $count) = $query->execute();
		return (bool) $count;
	}

	public function remove(Contact $contact)
	{
		if (!$contact->id)
		{
			throw new Exception("Contact does not have an id");
		}

		$query = DB::delete(self::TABLE)
			->where('id', '=', $contact->id)
			;
		$count = $query->execute();
		return (bool) $count;
	}

	public function edit(Contact $contact)
	{
		$data = array_filter($contact->asArray());
		unset($data['id']); // never update id
		$query = DB::update(self::TABLE)
			->set($data)
			->where('id', '=', $contact->id)
			;
		$count = $query->execute();
		return TRUE;
	}
}
