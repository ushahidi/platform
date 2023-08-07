<?php

namespace Ushahidi\Modules\V5\Repository\HXL;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ushahidi\Modules\V5\Models\HXL;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\HXLTagSearchFields;
use Ushahidi\Modules\V5\DTO\HXLMetadataSearchFields;
use Ushahidi\Modules\V5\DTO\HXLOrganizationSearchFields;
use Ushahidi\Modules\V5\DTO\HXLLicenseSearchFields;


use Ushahidi\Core\Entity\HXL as HXLEntity;

interface HXLRepository
{

    /**
     * This method will fetch list of the HXL Tags for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param HXLTagSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetchTags(Paging $paging, HXLTagSearchFields $search_fields): LengthAwarePaginator;


     /**
     * This method will fetch list of the HXL Tags for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param HXLOrganizationSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetchOrganization(Paging $paging, HXLOrganizationSearchFields $search_fields): LengthAwarePaginator;



     /**
     * This method will fetch list of the HXL Tags for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param HXLMetadataSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetchMetadata(Paging $paging, HXLMetadataSearchFields $search_fields): LengthAwarePaginator;



     /**
     * This method will fetch list of the HXL Tags for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @param HXLLicenseSearchFields user_search_fields
     * @return LengthAwarePaginator
     */
    public function fetchLicenses(Paging $paging, HXLLicenseSearchFields $search_fields): LengthAwarePaginator;

   
    /**
     * This method will create a HXL
     * @param HXLEntity $entity
     * @return int
     */
    public function create(HXLEntity $entity): int;
}
