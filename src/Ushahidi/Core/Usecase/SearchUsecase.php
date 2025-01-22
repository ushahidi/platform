<?php

/**
 * Ushahidi Platform Search Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Usecase;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Core\Usecase\Concerns\Formatter as FormatterTrait;
use Ushahidi\Core\Usecase\Concerns\Authorizer as AuthorizerTrait;
use Ushahidi\Core\Usecase\Concerns\Translator as TranslatorTrait;
use Ushahidi\Core\Concerns\FilterRecords;
use Ushahidi\Contracts\Repository\SearchRepository;

class SearchUsecase implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        TranslatorTrait;

    // - FilterRecords for setting search parameters
    use FilterRecords;

    /**
     * @var SearchData
     */
    protected $search;

    /**
     * @param SearchData $search
     */
    public function setData(SearchData $search)
    {
        $this->search = $search;
    }

    /**
     * @var SearchRepository
     */
    protected $repo;

    /**
     * Inject a repository that can search for entities.
     *
     * @param  SearchRepository $repo
     * @return $this
     */
    public function setRepository(SearchRepository $repo)
    {
        $this->repo = $repo;
        return $this;
    }

    // Usecase
    public function isWrite()
    {
        return false;
    }

    // Usecase
    public function isSearch()
    {
        return true;
    }

    // Usecase
    public function interact()
    {
        // Fetch an empty entity...
        $entity = $this->getEntity();

        // ... verify that the entity can be searched by the current user
        $this->verifySearchAuth($entity);

        // ... and get the search filters for this entity
        $search = $this->getSearch();

        // ... pass the search information to the repo
        $this->repo->setSearchParams($search);

        // ... get the results of the search
        $results = $this->repo->getSearchResults();

        // ... get the total count for the search
        $total = $this->repo->getSearchTotal();

        // ... remove any entities that cannot be seen
        $priv = 'read';
        foreach ($results as $idx => $entity) {
            if (!$this->auth->isAllowed($entity, $priv)) {
                unset($results[$idx]);
            }
        }

        // ... pass the search information to the formatter, for paging
        $this->formatter->setSearch($search, $total);

        // ... and return the formatted results.
        return $this->formatter->__invoke($results);
    }

    /**
     * Get an empty entity.
     *
     * @return Entity
     */
    protected function getEntity()
    {
        return $this->repo->getEntity();
    }

    /**
     * Get filter parameters and default values that are used for paging.
     *
     * @return array
     */
    protected function getPagingFields()
    {
        return [
            'orderby' => 'id',
            'order'   => 'asc',
            'limit'   => null,
            'offset'  => 0
        ];
    }

    /**
     * Get filter parameters as search data.
     *
     * @return SearchData
     */
    protected function getSearch()
    {
        // Get possible search fields from the repo
        $fields = $this->repo->getSearchFields();
        // Get possible fields for paging
        $paging = $this->getPagingFields();

        // Get filter values for both paging and search fields
        $filters = $this->getFilters(array_merge($fields, array_keys($paging)));

        // Merge default paging values, and user input
        // and save that to search data
        $this->search->setFilters(array_merge($paging, $filters));
        // Flag sorting values in search data
        $this->search->setSortingKeys(array_keys($paging));

        return $this->search;
    }

    public function getSearchTotal()
    {
        return $this->repo->getSearchTotal();
    }
}
