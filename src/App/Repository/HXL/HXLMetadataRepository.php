<?php

/**
 * Ushahidi HXLMetadata Repository, using Kohana::$config
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
namespace Ushahidi\App\Repository\HXL;

use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\HXL\HXLMetadata;
use Ushahidi\Core\Entity\HXL\HXLMetadataRepository as HXLMetadataRepositoryContract;
use Ushahidi\App\Repository\OhanzeeRepository;

class HXLMetadataRepository extends OhanzeeRepository implements
    HXLMetadataRepositoryContract
{
    // OhanzeeRepository
    protected function getTable()
    {
        return 'hxl_meta_data';
    }

    public function getSearchFields()
    {
        return ['dataset_title', 'export_job_id'];
    }


    /**
     * @param SearchData $search
     * Search by dataset_title and export_job_id
     */
    public function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;
        if ($search->dataset_title) {
            $query->where('dataset_title', '=', $search->dataset_title);
        }
        if ($search->export_job_id) {
            $query->where('export_job_id', '=', $search->export_job_id);
        }
        return $query;
    }

    public function getEntity(array $data = null)
    {
        return new HXLMetadata($data);
    }

    // FormAttributeRepository
    public function getByJobId($jobId)
    {
        $query = $this->selectQuery(['export_job_id' => $jobId])
            ->select("{$this->getTable()}.*")
            ->limit(1);

        $result = $query->execute($this->db);
        return $this->getEntity($result->current());
    }
}
