<?php

/**
 * Ushahidi Platform Validator Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator;

use Ushahidi\Core\Tool\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class LumenValidator implements ValidatorContract
{
    // Regex that only allows letters, numbers, punctuation, and space.
    const REGEX_STANDARD_TEXT = '/^[\pL\pN\pP ]++$/uD';

    protected $fullData;
    protected $validator;

    /**
     * Must return an array of rules that the validator should apply
     *
     * @return  Array  $rules array of $key => $rule
     */
    abstract protected function getRules();

    /**
     * Check the data against the rules returned by getRules()
     *
     * @param  Array $data      an array of changed values to check in $key => $value format
     * @param  Array $fullData  an array of full entity data for reference during validation
     * @return bool
     */
    public function check(array $data, array $fullData = []) : bool
    {
        // If no full data is passed, fallback to changed values
        if (!$fullData) {
            $fullData = $data;
        }
        $this->fullData = $fullData;

        $this->validator = Validator::make($data, $this->getRules());

        return $this->validator->passes();
    }

    /**
     * Return an array of any errors that occurred during validation
     *
     * @return Array
     */
    public function errors() : array
    {
        return $this->validator->errors()->toArray();
    }
}
