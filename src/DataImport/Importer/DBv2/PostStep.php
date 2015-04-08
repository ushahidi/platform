<?php

/**
 * Ushahidi Platform DBv2 Post Import Step
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport\Importer\DBv2;

use Ushahidi\DataImport\ImportStep;
use Ushahidi\DataImport\WriterTrait;
use Ushahidi\DataImport\ResourceMapTrait;

use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\WriterInterface;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;

class PostStep implements ImportStep
{
	use WriterTrait, ResourceMapTrait;

	/**
	 * Get post reader
	 * @return Ddeboer\DataImport\Reader
	 */
	protected function getReader(\PDO $connection)
	{
		$incidentReader = new Reader\PdoReader($connection,
			"SELECT i.*,
				i.id AS incident_id,
				location_name,
				latitude,
				longitude,
				person_first,
				person_last,
				person_email
			FROM incident i
			LEFT JOIN incident_person p ON (i.id = p.incident_id)
			LEFT JOIN location l ON (i.location_id = l.id)
			ORDER BY i.id ASC
			"
		);

		$incidentMediaReader = new Reader\PdoReader($connection,
			"SELECT media.*
			FROM media
			WHERE incident_id IS NOT NULL
			AND media_type = 4
			ORDER BY incident_id ASC
			"
		);

		$incidentCategoryReader = new Reader\PdoReader($connection,
			"SELECT incident_id, category_id
			FROM incident_category
			ORDER BY incident_id ASC
			"
		);

		// Note we have to sort by incident_id and incident.id in the other readers or OneToManyReader loses rows
		// return new Reader\OneToManyReader(
		// 	new Reader\OneToManyReader($incidentReader, $incidentCategoryReader, 'categories', 'incident_id'),
		// 	$incidentMediaReader, 'news', 'incident_id'
		// );

		return new Reader\OneToManyReader($incidentReader, $incidentCategoryReader, 'categories', 'incident_id');

	}

	/**
	 * Item transform callback
	 * @param  Array  $item
	 * @return Array
	 */
	public function transform($item)
	{
		$resourceMap = $this->resourceMap;
		$tags = array_filter(array_map(function($category) use ($resourceMap) {
			return $resourceMap->getMappedId('tag', $category['category_id']);
		}, $item['categories']));

		return [
			'original_id' => $item['id'],
			'title' => $item['incident_title'],
			'content' => $item['incident_description'],
			'status' => $item['incident_active'] ? 'published' : 'draft',
			'author_email' => $item['person_email'],
			'form_id' => $this->resourceMap->getMappedId('form', $item['form_id']),
			'user_id' => $this->resourceMap->getMappedId('user', $item['user_id']),
			'tags' => $tags,
			'values' => []
		];
	}

	/**
	 * Run a data import step
	 *
	 * @return mixed
	 */
	public function run(Array $options)
	{
		$workflow = new Workflow($this->getReader($options['connection']), $options['logger'], 'dbv2-incidents');
		$result = $workflow
			->addWriter($this->getWriter())
			->addItemConverter(new CallbackItemConverter([$this, 'transform']))
			->setSkipItemOnFailure(false)
			->process()
		;

		return $result;
	}
}
