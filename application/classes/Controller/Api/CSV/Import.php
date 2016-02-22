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

	protected $_action_map = array
	(
		Http_Request::POST    => 'post',   // Typically Create..
		Http_Request::OPTIONS => 'options'
	);

	protected function _scope()
	{
		return 'csv';
	}

	protected function _resource()
	{
		return 'posts';
	}

	public function action_post_index_collection()
	{
		// Get payload from CSV repo
		$csv = service('repository.csv')->get($this->request->param('csv_id'));

		$fs = service('tool.filesystem');
		$reader = service('filereader.csv');
		$transformer = service('transformer.csv');

		// Read file
		$file = new SplTempFileObject();
		$contents = $fs->read($csv->filename);
		$file->fwrite($contents);

		// Get records
		// @todo read up to a sensible offset and process the rest later
		$records = $reader->process($file);

		// Set map and fixed values for transformer
		$transformer->setMap($csv->maps_to);
		$transformer->setFixedValues($csv->fixed);

		$this->_usecase = service('factory.usecase')
						->get($this->_resource(), 'import')
						->setPayload($records)
						->setTransformer($transformer);
	}
}
