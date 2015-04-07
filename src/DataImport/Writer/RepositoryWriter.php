<?php

/**
 * Ushahidi Platform Data Import Repository Writer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport\Writer;

use Ddeboer\DataImport\Writer\WriterInterface;
use Ushahidi\Core\Usecase\CreateRepository;
use \Ddeboer\DataImport\Exception\WriterException;

abstract class RepositoryWriter implements WriterInterface
{
	// MappingWriterTrait records the new id against old id
	use MappingWriterTrait;

	protected $repo;

	/**
	 * @param Repository $repo
	 */
	public function __construct(CreateRepository $repo)
	{
		$this->repo = $repo;
	}

	/**
	 * Create a Entity from item
	 * @param  Array $item
	 * @return Entity
	 */
	abstract protected function createEntity(array $item);

	/**
	 * {@inheritDoc}
	 */
	public function prepare()
	{
		// Clean out mapping array
		$this->map = [];

		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function writeItem(array $item)
	{
		$data = $this->createEntity($item);

		try {
			$newid = $this->repo->create($data);

			// Add to map
			$this->setMappedId($item, $newid);
		} catch (\Exception $e) {
			// Convert exception so the abstracton doesn't leak
			throw new WriterException('Write failed ('.$e->getMessage().').', null, $e);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function finish()
	{
		return $this;
	}
}
