<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Contact Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Usecase\CreateRepository;
use Ushahidi\Core\Usecase\UpdateRepository;
use Ushahidi\Core\Usecase\SearchRepository;

class Ushahidi_Repository_Contact extends Ushahidi_Repository implements
	ContactRepository, CreateRepository, UpdateRepository, SearchRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'contacts';
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new Contact($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return [
			'contact', 'type', 'user', 'data_provider'
		];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		foreach ([
			'user',
		] as $fk)
		{
			if ($search->$fk)
			{
				$query->where("contacts.{$fk}_id", '=', $search->$fk);
			}
		}

		foreach ([
			'type',
			'data_provider',
			'contact'
		] as $key)
		{
			if ($search->$key)
			{
				$query->where("contacts.{$key}", '=', $search->$key);
			}
		}
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$state = [
			'created'  => time(),
		];

		return parent::create($entity->setState($state));
	}

	// UpdateRepository
	public function update(Entity $entity)
	{
		$state = [
			'updated'  => time(),
		];

		return parent::update($entity->setState($state));
	}

	// ContactRepository
	public function getByContact($contact, $type)
	{
		return $this->getEntity($this->selectOne(compact('contact', 'type')));
	}
}
