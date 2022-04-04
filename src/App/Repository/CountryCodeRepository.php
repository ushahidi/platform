<?php

/**
 * Ushahidi Config Repository, using Kohana::$config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository;

use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Core\Entity\CountryCode;
use Ushahidi\Contracts\Repository\ReadRepository;
use Ushahidi\Contracts\Repository\SearchRepository;
use Ushahidi\Contracts\Repository\Entity\CountryCodeRepository as CountryCodeRepositoryContract;

class CountryCodeRepository extends OhanzeeRepository implements
    CountryCodeRepositoryContract,
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
