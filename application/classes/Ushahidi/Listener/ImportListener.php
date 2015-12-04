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
use Ushahidi\Core\Tool\ReaderFactory;

class Ushahidi_Listener_ImportListener extends AbstractListener
{
	protected $repo;
	protected $fs;
	protected $reader;
	
	public function setReader($reader)
	{
		$this->reader = $reader;
	}

	public function setImportUsecase(ImportUsecase $usecase)
	{
		$this->usecase = $usecase;
	}

	public function setFilesystem(Filesystem $fs)
	{
		$this->fs = $fs;
	}
	
    public function handle(EventInterface $event, Entity $entity = null)
    {
		// @todo Get a mapper and import use case
    }

	private function get_contents($filename)
	{
		$file = new SplTempFileObject();
		$stream = $this->fs->readStream($filename);
		$file->fwrite(stream_get_contents($stream));

		// @todo Read upto a sensible offset and queue processing for the rest
		return $this->reader->process($file);
	}


}
