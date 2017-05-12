<?php

/**
 * Ushahidi CSV Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\CSV;
use Ushahidi\Core\Entity\CSVRepository;

use League\Event\ListenerInterface;
use Ushahidi\Core\Traits\Event;

class CSV extends OhanzeeRepository implements
	CSVRepository
{

	// Use the JSON transcoder to encode properties
	use JsonTranscodeRepository;

	// Use Event trait to trigger events
	use Event;

	// JsonTranscodeRepository
	protected function getJsonProperties()
	{
		return ['columns', 'maps_to', 'fixed'];
	}

	// OhanzeeRepository
	protected function getTable()
	{
		return 'csv';
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

	// OhanzeeRepository
	public function getEntity(Array $data = null)
	{
		return new CSV($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['columns', 'maps_to', 'fixed', 'filename'];
	}

}
