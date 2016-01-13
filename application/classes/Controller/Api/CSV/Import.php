<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API CSV Import
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_API_CSV_Import extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'posts';
	}

	public function action_post_import_collection()
	{
		// Get payload from CSV repo
		$csv = service('repository.csv')->get($this->request->param('csv_id'));

		$fs = service('tool.filesystem');
		$reader = service('filereader.csv');
		$transformer = service('transformer.csv');

		// Read file
		$file = new SplTempFileObject();
		$stream = $fs->readStream($csv->filename);
		$file->fwrite(stream_get_contents($stream));

		// Get records
		// @todo read up to a sensible offset and process the rest later
		$records = $reader->process($file);

		// Set map and fixed values for transformer
		$transformer->setMap($csv->maps_to);
		$transformer->setFixedValues($csv->fixed);

		$this->_usecase = service('factory.usecase')
						->get($this->_scope(), 'import')
						->setPayload($records)
						->setTransformer($transformer);
	}
}
