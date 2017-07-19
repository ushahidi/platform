<?php

/**
 * Ushahidi Platform Entity Search Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Tos;

use Ushahidi\Core\Usecase\SearchUsecase;

class SearchTos extends SearchUsecase
{
    /**
     * Get filter parameters that are used for paging.
     *
     * @return Array
     */
    protected function getPagingFields()
    {
        return [
            'orderby' => 'agreement_date',
            'order'   => 'desc',
            'limit'   => null,
            'offset'  => 0
        ];
    }
}

//why is this not running?