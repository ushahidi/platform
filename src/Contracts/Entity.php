<?php

/**
 * Ushahidi Platform Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts;

interface Entity
{
    /**
     * Return the unique identity for the entity.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Return the resource name for this entity.
     *
     * @return string
     */
    public function getResource();

    /**
     * Get the current entity state as an associative array.
     *
     * @return array
     */
    public function asArray();

    /**
     * Change the internal state of the entity, updating values and tracking any
     * changes that are made.
     *
     * @param  array  $data
     *
     * @return $this
     */
    public function setState(array $data);

    /**
     * Check if a property has been changed.
     *
     * @param  string $key
     * @param  string $array_key the sub key we want to check, presently we
     *         only go one level deep within nested arrays
     * @return boolean
     */
    public function hasChanged($key, $array_key = null);

    /**
     * Get all values that have been changed since initial state was defined.
     *
     * @return array
     */
    public function getChanged();
}
