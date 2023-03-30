<?php

/**
 * Ushahidi Config Repository, using Kohana::$config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Repositories;

use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Core\Ohanzee\Entities\CountryCode;
use Ushahidi\Core\Contracts\Repository\EntityGet;
use Ushahidi\Core\Contracts\Repository\ReadRepository;
use Ushahidi\Core\Contracts\Repository\SearchRepository;

class CountryCodeRepository extends OhanzeeRepository implements
    EntityGet,
    ReadRepository,
    SearchRepository
{
    // OhanzeeRepository
    protected function getTable()
    {
        return 'country_codes';
    }

    public function getSearchFields()
    {
        return ['country_code', 'dial_code'];
    }

    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        return $query;
    }

    public function getEntity(array $data = null)
    {
        return new CountryCode($data);
    }
}
