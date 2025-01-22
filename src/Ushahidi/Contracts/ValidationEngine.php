<?php

/**
 * Ushahidi Core ValidationEngine Interface
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Core
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts;

interface ValidationEngine
{
    /**
     * Set or reset the data to be validated
     *
     * @param Array $data array of data in $key => $value format
     */
    public function setData(array $data);

    /**
     * Get data by its array key
     *
     * @param  string $key
     * @return mixed
     */
    public function getData($key = null);

    /**
     * Set or reset the full data array to be referenced for validation
     *
     * @param Array $data array of data in $key => $value format
     */
    public function setFullData(array $data);

    /**
     * Get full data by its array key
     *
     * @param  string $key
     * @return mixed
     */
    public function getFullData($key = null);

    /**
     * Set rules that the validator will apply against the data
     *
     * @return null
     */
    public function rules($field, array $rules);

    /**
     * Check the data against the previously set rules
     *
     * @return bool
     */
    public function check();

    /**
     * Add an error to a field.
     *
     * @param  string $field  field name
     * @param  string $error  error message
     * @param  array  $params
     * @return $this
     */
    public function error($field, $error, array $params = null);

    /**
     * Get any errors that occurred during validation
     * optionally load messages from a $file
     * and $translate them into the default language (or a given language)
     *
     * @param  string $file      file containing custom error messages
     * @param  mixed  $translate boolean or string representing a language
     * @return array
     */
    public function errors($file = null, $translate = true);
}
