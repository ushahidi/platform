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
use Ushahidi\Core\Tool\MappingTransformer;
use Ushahidi\Core\Tool\FileReader;
use Ushahidi\Core\Usecase\ImportUsecase;

use Ushahidi\Core\Entity\PostRepository;

class Ushahidi_Listener_CSVImportListener extends AbstractListener
{
	protected $repo;
	protected $fs;
	protected $reader;
	protected $transformer;
	
	public function setReader(FileReader $file_reader)
	{
		$this->file_reader = $file_reader;
	}

	public function setRepo(PostRepository $repo)
	{
		$this->repo = $repo;
	}

	public function setFilesystem(Filesystem $fs)
	{
		$this->fs = $fs;
	}
	
    public function handle(EventInterface $event, Entity $entity = null)
    {
		$payload = $this->process($entity->filename);


		foreach ($payload as $record) {
			// remap columns
			$record = $this->remap($entity->maps_to, $record);

			$this->create($record, $entity);
		}
    }

	private function process($filename)
	{
		$file = new SplTempFileObject();
		$stream = $this->fs->readStream($filename);
		$file->fwrite(stream_get_contents($stream));

		// @todo Read upto a sensible offset and queue processing for the rest
		return $this->file_reader->process($file);
	}

	private function create($record, $entity)
	{
		$post_entity = $this->repo->getEntity();

		$post_fields = $post_entity->asArray();

		// Filter post fields from the record
		$post_fields = array_intersect_key($record, $post_fields);

		// Remove post fields from the record and leave form values
		foreach ($post_fields as $key => $val) {
			unset($record[$key]);
		}

		// Put values in array
		array_walk($record, function (&$val) {
				$val = [$val];
		});

		// Add left over values
		$record = array_merge($record, $entity->unmapped);
		$form_values = ['values' => $record];

		// Prepare fixed values
		$fixed = ['form'         => $entity->form_id,
				  'tags'         => $entity->tags,
				  'published_to' => $entity->published_to,
				  'status'       => $entity->status
		];

		// Set state
		$state = array_merge($post_fields, $form_values, $fixed);

		$post_entity->setState($state);
		$this->repo->create($post_entity);
	}
	
	private function remap($columns, $record)
	{
		$record = array_values($record);

		// Don't import columns marked as NULL
		foreach ($columns as $index => $column) {
			if ($column === NULL) {
				unset($columns[$index]);
				unset($record[$index]);
			}
		}

		// Remap record columns
		return array_combine($columns, $record);
	}
}
