<?php

/**
 * Ushahidi JsonTranscode Utility
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Core
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

class JsonTranscode
{
    /**
     * JSON encode the values of $input identified by $properties
     * $input can be either an array or an object
     *
     * @param  mixed $input      array or object
     * @param  array $properties property names to encode
     * @return mixed             returns $input with json-encoded properties
     */
    public function encode($input, $properties)
    {
        return $this->transcode($input, $properties, function ($property) {
            return json_encode($property);
        });
    }

    /**
     * JSON decode the properties of $input (array or object)
     * defined as strings in the $properties array.
     *
     * @param  mixed $input      array or object
     * @param  array $properties property names to decode
     * @return mixed             returns $input with json-decoded properties
     */
    public function decode($input, $properties)
    {
        return $this->transcode($input, $properties, function ($property) {
            return json_decode($property, true);
        });
    }

    /**
     * JSON-encode or -decode properties of an array or object
     * according to the properties given
     * @param  mixed   $input          array or object
     * @param  array   $properties     property names or array keys to decode
     * @param  Closure $transcode_func a function which returns the transcoded value
     * @return mixed                   returns $input with decoded/encoded properties
     */
    private function transcode($input, $properties, $transcode_func)
    {
        if (!$this->isTranscodable($input)) {
            return $input;
        }

        $is_object = is_object($input);
        $is_array  = is_array($input);

        foreach ($properties as $p) {
            if (($is_object && !property_exists($input, $p))
             || ($is_array && !array_key_exists($p, $input))
            ) {
                continue;
            }

            if ($is_object) {
                $input->$p = $transcode_func($input->$p);
            } elseif ($is_array) {
                $input[$p] = $transcode_func($input[$p]);
            }
        }

        return $input;
    }

    private function isTranscodable($input)
    {
        if ($input === null) {
            return false;
        }

        if (!is_object($input) && !is_array($input)) {
            return false;
        }

        return true;
    }
}
