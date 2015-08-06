<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Ushahidi Json Transcoding Repo Trait
 *
 * JSON encodes properties defined `json_properties`
 * during `executeUpdate` and `executeInsert`
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\JsonTranscode;

trait Ushahidi_JsonTranscodeRepository {

	protected $json_transcoder;

	/**
	 * Return an array of properties to be json encoded
	 * @return Array
	 */
	abstract protected function getJsonProperties();

	public function setTranscoder(JsonTranscode $transcoder)
	{
		$this->json_transcoder = $transcoder;
	}

	// Ushahidi_Repository
	public function executeInsert(Array $input)
	{
		// JSON Encode defined properties
		$input = array_filter($this->json_transcoder->encode(
			$input,
			$this->getJsonProperties()
		));

		return parent::executeInsert($input);
	}

	// Ushahidi_Repository
	public function executeUpdate(Array $where, Array $input)
	{
		// JSON Encode defined properties
		$input = $this->json_transcoder->encode(
			$input,
			$this->getJsonProperties()
		);

		return parent::executeUpdate($where, $input);
	}

}
