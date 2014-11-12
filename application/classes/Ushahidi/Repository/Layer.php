<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Layer Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Layer;
use Ushahidi\Core\Tool\JsonTranscode;

class Ushahidi_Repository_Layer extends Ushahidi_Repository
{
	protected $json_transcoder;
	protected $json_properties = ['options'];

	public function setTranscoder(JsonTranscode $transcoder)
	{
		$this->json_transcoder = $transcoder;
	}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'layers';
	}

	// Ushahidi_Repository
	public function getEntity(Array $data = null)
	{
		$data = $this->json_transcoder->decode($data, $this->json_properties);
		return new Layer($data);
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;

		if ($search->active !== null) {
			$query->where('active', '=', $search->active);
		}

		if ($search->type) {
			$query->where('type', '=', $search->type);
		}
	}

	// CreateRepository
	public function create(Data $input)
	{
		$record = $this->json_transcoder->encode(
			$input, $this->json_properties
		)->asArray();
		$record['created'] = time();
		return $this->executeInsert($record);
	}

	// UpdateRepository
	public function update($id, Data $input)
	{
		$update = $this->json_transcoder->encode(
			$input, $this->json_properties
		)->asArray();
		$update['updated'] = time();
		return $this->executeUpdate(compact('id'), $update);
	}
}
