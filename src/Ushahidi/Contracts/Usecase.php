<?php

/**
 * Ushahidi Platform Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts;

interface Usecase
{
    /**
     * Will this usecase write any data?
     *
     * @return boolean
     */
    public function isWrite();

    /**
     * Will this usecase search for data?
     *
     * @return boolean
     */
    public function isSearch();

    /**
     * Given user input, return a formatted Entity as the result.
     *
     * Interaction will typically consist of one or more of the following:
     *
     * - verifying user input
     * - checking user authorization
     * - creating a new entity
     * - reading one or more entities
     * - updating an entity
     * - deleting an entity
     *
     * @return array|\Ushahidi\Contracts\Entity[]
     */
    public function interact();
}
