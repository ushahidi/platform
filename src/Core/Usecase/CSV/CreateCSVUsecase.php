<?php

/**
 * Ushahidi Platform CSV Create Usecase
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\CSV;

use SplTempFileObject;
use Ushahidi\Core\Tool\ReaderFactory;
use Ushahidi\Core\Usecase\Media\CreateMedia;
use Ushahidi\Core\Tool\UploadData;
use Ddeboer\DataImport\Step\MappingStep;

class CreateCSVUsecase extends CreateMedia
{
	/**
	 * @var ReaderFactory
	 */
	protected $reader_factory;

	/**
	 * @param  ReaderFactory $reader_factory
	 * @return $this;
	 */

	public function setReaderFactory(ReaderFactory $reader_factory)
	{
		$this->reader_factory = $reader_factory;
	}

	/**
	 * Get an empty entity, apply the payload.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		$upload_data = new UploadData($this->getPayload('file'));
		
		// Upload the file and get the file reference
		$this->upload = $this->uploader->upload($upload_data);

		// Get SplFileObject for the CSV Reader
		$file = new SplTempFileObject();
		
		$stream = fopen($upload_data->tmp_name, 'r+');
		$file->fwrite(stream_get_contents($stream));

		// Create a reader and fetch CSV columns
		$reader = $this->reader_factory->createReader($file);
		$columns = $reader->fetchOne();

		$payload = [
			'columns'    => $columns,
			'filename'   => $this->upload->file,
			'mime'       => $this->upload->type,
			'size'       => $this->upload->size,
		];
		
		return $this->repo->getEntity()->setState($payload);
	}
}
