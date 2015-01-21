<?php

/**
 * Ushahidi Platform Media Create Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Media;

use Ushahidi\Core\Tool\Filesystem;
use Ushahidi\Core\Tool\Uploader;
use Ushahidi\Core\Tool\UploadData;
use Ushahidi\Core\Usecase\CreateUsecase;
use Ushahidi\Exception\ValidatorException;

class CreateMedia extends CreateUsecase
{
	/**
	 * @var Filesystem
	 */
	protected $filesystem;

	/**
	 * @var Uploader
	 */
	protected $uploader;

	/**
	 * @var UploadData
	 */
	protected $upload;

	/**
	 * @param  Filesystem $fs
	 * @return $this;
	 */
	public function setFilesystem(Filesystem $fs)
	{
		$this->fs = $fs;
		return $this;
	}

	/**
	 * @param  Uploader $upload
	 * @return $this
	 */
	public function setUploader(Uploader $uploader)
	{
		$this->uploader = $uploader;
		return $this;
	}

	// Usecase
	public function interact()
	{
		try {
			return parent::interact();
		} catch (ValidatorException $e) {
			// If a file was uploaded, it must be purged after a failed upload.
			// Otherwise storage will be filled with junk files.
			if ($this->fs->has($this->upload->file)) {
				$this->fs->delete($this->upload->file);
			}

			// Pass the exception.
			throw $e;
		}
	}

	/**
	 * Get an empty entity, apply the payload.
	 *
	 * @return Entity
	 */
	protected function getEntity()
	{
		// Upload the file and get the file reference
		$this->upload = $this->uploader->upload(
			new UploadData($this->getPayload('file'))
		);

		$payload = [
			'caption'    => $this->getPayload('caption'),
			'user_id'    => $this->getPayload('user_id', false) ?: null,
			'mime'       => $this->upload->type,
			'o_filename' => $this->upload->file,
			'o_size'     => $this->upload->size,
			'o_width'    => $this->upload->width,
			'o_height'   => $this->upload->height,
		];

		return $this->repo->getEntity()->setState($payload);
	}
}
