<?php

/**
 * Ushahidi Platform Data Import Form Writer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport\Writer;

use Ushahidi\Core\Entity\Form;
use Ushahidi\Core\Entity\FormGroup;
use Ushahidi\Core\Entity\FormAttribute;
use \Ddeboer\DataImport\Exception\WriterException;
use Ushahidi\Core\Usecase\CreateRepository;

class FormWriter extends RepositoryWriter
{
	protected $groupRepo;
	protected $attributeRepo;

	/**
	 * @param Repository $repo
	 */
	public function __construct(CreateRepository $repo, $groupRepo, $attributeRepo)
	{
		$this->repo = $repo;
		$this->groupRepo = $groupRepo;
		$this->attributeRepo = $attributeRepo;
	}

	// RepositoryWriter
	protected function createEntity(array $item)
	{
		return new Form($item);
	}

	// RepositoryWriter
	public function createAttribute(array $item)
	{
		return new FormAttribute($item);
	}

	// RepositoryWriter
	protected function createStructureGroup($formId)
	{
		return new FormGroup([
				'label' => 'Structure',
				'form_id' => $formId
			]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function writeItem(array $item)
	{
		$data = $this->createEntity($item);

		try {
			// Create form
			$formId = $this->repo->create($data);

			// Create group
			$structureGroup = $this->createStructureGroup($formId);
			$form_group_id = $this->groupRepo->create($structureGroup);

			// Create attributes
			foreach($item['attributes'] as $attr) {
				$this->attributeRepo->create(
					$this->createAttribute($attr + compact('form_group_id'))
				);
			}

			// Add to map
			$this->setMappedId($item, $formId);
		} catch (\Exception $e) {
			// Convert exception so the abstracton doesn't leak
			throw new WriterException('Write failed ('.$e->getMessage().').', null, $e);
		}
	}

}
