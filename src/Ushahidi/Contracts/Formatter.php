<?php

/**
 * Ushahidi Platform Formatter Interface
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Contracts;

interface Formatter
{
    /**
     * @param  \Ushahidi\Contracts\Entity|\Ushahidi\Contracts\Entity[]|mixed $data
     *
     * @return mixed
     *
     * @throws \Ushahidi\Core\Exception\FormatterException
     */
    public function __invoke($data);
}
