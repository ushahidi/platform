<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Import Listener
 *
 * Imports posts from CSV file when all the fields are available
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Filesystem;
use League\Event\AbstractListener;
use League\Event\EventInterface;
use Ushahidi\Core\Tool\FileReader;
use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Core\Tool\MappingTransformer;

class Ushahidi_Listener_CSVPostListener extends AbstractListener
{
	protected $repo;
	protected $fs;
	protected $fileReader;
	protected $createUsecase;
	protected $transformer;
	
	public function setReader(FileReader $fileReader)
	{
		$this->fileReader = $fileReader;
	}

	public function setTransformer(MappingTransformer $transformer)
	{
		$this->transformer = $transformer;
	}

	public function setFilesystem(Filesystem $fs)
	{
		$this->fs = $fs;
	}

	public function setUsecase(CreateUsecase $usecase)
	{
		$this->usecase = $usecase;
	}

    public function handle(EventInterface $event, Entity $entity = null)
    {
		$file = new SplTempFileObject();
		$stream = $this->fs->readStream($entity->filename);
		$file->fwrite(stream_get_contents($stream));

		// @todo Read up to a sensible offset and queue the rest for processing
		$records = $this->fileReader->process($file);

		$this->transformer->setMap($entity->maps_to);
		$this->transformer->setFixedValues([
			'form'         => $entity->form_id,
			'tags'         => $entity->tags,
			'published_to' => $entity->published_to,
			'status'       => $entity->status
		]);
		$this->transformer->setUnmapped($entity->unmapped);

		foreach ($records as $record) {
			$record = $this->transformer->interact($record);
			$this->usecase->setPayload($record)->interact();
		}

		// Delete the file here for now.
		// @todo Check if the the whole file was processed here before deleting.
		$this->fs->delete($entity->filename);
    }
}
