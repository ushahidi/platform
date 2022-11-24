<?php

namespace Ushahidi\Modules\V5\Repository\Tos;

use Ushahidi\Modules\V5\Models\Tos;
use Ushahidi\Modules\V5\Repository\Tos\TosRepository as TosTosRepository;
use Ushahidi\Core\Exception\NotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentTosRepository implements TosTosRepository
{
    /**
     * This method will fetch all the Tos for the logged user from the database utilising
     * Laravel Eloquent ORM and return them as an array
     * @param int $limit
     * @param int $skip
     * @param string $sortBy
     * @param string $order
     * @return Tos[]
     */
    public function fetch(int $limit, int $skip, string $sortBy, string $order): LengthAwarePaginator
    {
        return Tos::take($limit)
        ->skip($skip)
        ->orderBy($sortBy, $order)
        ->paginate($limit);
    }

    /**
     * This method will fetch a single Tos from the database utilising
     * Laravel Eloquent ORM. Will throw an exception if provided identifier does
     * not exist in the database.
     * @param int $id
     * @return Tos
     * @throws NotFoundException
     */
    public function findById(int $id): Tos
    {
        $tos = Tos::find($id);
        if (!$tos instanceof Tos) {
            throw new NotFoundException('tos not found');
        }
        return $tos;
    }

    /**
     * This method will create a Tos
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function create(array $input): int
    {
        DB::beginTransaction();
        try {
            $tos = tos::create($input);
            DB::commit();
            return $tos->id;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
