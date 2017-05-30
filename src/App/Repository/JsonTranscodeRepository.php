<?php

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

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Tool\JsonTranscode;

trait JsonTranscodeRepository
{

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

    // Temporary override function for attribute addition
    public function executeInsertAttribute(array $input)
    {
        // JSON Encode defined properties
        $input = $this->json_transcoder->encode(
            $input,
            $this->getJsonProperties()
        );

        return parent::executeInsert($input);
    }

    // OhanzeeRepository
    public function executeInsert(array $input)
    {
        // JSON Encode defined properties
        // The use of array_filter causes issues with array items set as 0
        // the items are removed. This code should ultimately be refactored.
        $input = array_filter($this->json_transcoder->encode(
            $input,
            $this->getJsonProperties()
        ));

        return parent::executeInsert($input);
    }

    // OhanzeeRepository
    public function executeUpdate(array $where, array $input)
    {
        // JSON Encode defined properties
        $input = $this->json_transcoder->encode(
            $input,
            $this->getJsonProperties()
        );

        return parent::executeUpdate($where, $input);
    }
}
