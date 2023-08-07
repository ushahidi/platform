<?php

namespace Ushahidi\Modules\V5\Repository\HXL;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\HXL;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\HXLTagSearchFields;
use Ushahidi\Modules\V5\DTO\HXLMetadataSearchFields;
use Ushahidi\Modules\V5\DTO\HXLOrganizationSearchFields;
use Ushahidi\Modules\V5\DTO\HXLLicenseSearchFields;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\HXL as HXLEntity;

class EloquentHXLRepository implements HXLRepository
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
    public function fetchTags(Paging $paging, HXLTagSearchFields $search_fields): LengthAwarePaginator
    {
        $builder = HXL\HXLTag::take($paging->getLimit())
            ->with('attributes')
            ->with('types')
            ->skip($paging->getSkip())
            ->orderBy($paging->getOrderBy(), $paging->getOrder());

        // set search conditions
        if ($search_fields->q()) {
            $builder->where(function ($query) use ($search_fields) {
                $query->where("hxl_tags.tag_name", "like", "%" . $search_fields->q() . "%");
                $query->orWhere("hxl_tags.description", "like", "%" . $search_fields->q() . "%");
            });
        }

        return $builder->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }


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
    public function fetchOrganization(Paging $paging, HXLOrganizationSearchFields $search_fields): LengthAwarePaginator
    {
        $builder = HXL\HXLTag::take($paging->getLimit())
            ->skip($paging->getSkip())
            ->orderBy($paging->getOrderBy(), $paging->getOrder());

        // set search conditions

        return $builder->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }



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
    public function fetchMetadata(Paging $paging, HXLMetadataSearchFields $search_fields): LengthAwarePaginator
    {
        $builder = HXL\HXLMetaData::take($paging->getLimit())
            ->skip($paging->getSkip())
            ->orderBy($paging->getOrderBy(), $paging->getOrder());

        // set search conditions
        if (count($search_fields->user())) {
            $builder->whereIn('hxls.user_id', $search_fields->user());
        }

        return $builder->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }



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
    public function fetchLicenses(Paging $paging, HXLLicenseSearchFields $search_fields): LengthAwarePaginator
    {
        $builder = HXL\HXLLicense::take($paging->getLimit())
            ->skip($paging->getSkip())
            ->orderBy($paging->getOrderBy(), $paging->getOrder());

        // set search conditions
        if ($search_fields->name()) {
            $builder->where("name", "like", "%" . $search_fields->name() . "%");
        }

        if ($search_fields->code()) {
            $builder->where("code", "like", "%" . $search_fields->code() . "%");
        }




        return $builder->paginate($paging->getLimit() ? $paging->getLimit() : config('paging.default_laravel_pageing_limit'));
    }



    /**
     * This method will create a HXL
     * @param HXLEntity $entity
     * @return int
     * @throws \Exception
     */
    public function create(HXLEntity $entity): int
    {
        DB::beginTransaction();
        try {
            $hxl = HXL::create($entity->asArray());
            DB::commit();
            return $hxl->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
