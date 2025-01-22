<?php

/**
 * Ushahidi Modify Records Trait
 *
 * Gives objects two methods:
 *
 * - `setPayload(Array $payload)`
 * - `getPayload($name, $default)`
 *
 * Used to set parameters for modifying a single record.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Concerns;

trait ModifyRecords
{
    /**
     * @var Array
     */
    protected $payload = [];

    /**
     * Set parameters that can be used to uniquely identify a **single** entity:
     *
     *     $obj->setPayload([
     *         'username'  => 'sally',
     *         'real_name' => 'Sally Jones',
     *         'age'       => 42,
     *     ]);
     *
     * @param  array $payload
     * @return $this
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Get a parameter by name. A default value can be provided, which will be
     * returned if the parameter does not exist. If no default is provided, and the
     * parameter does not exist, an exception will be thrown.
     *
     *     // Get a required parameter
     *     $username = $this->getPayload('username');
     *
     *     // Get an optional parameter, with a default
     *     $age = $this->getPayload('age', false);
     *
     * @throws \InvalidArgumentException
     */
    protected function getPayload(string $name, $default = null)
    {
        if (!isset($this->payload[$name])) {
            if (!isset($default)) {
                throw new \InvalidArgumentException(sprintf(
                    'Payload parameter %s has not been declared, defined parameters are: %s',
                    $name,
                    implode(', ', array_keys($this->payload))
                ));
            }
            return $default;
        }
        return $this->payload[$name];
    }
}
