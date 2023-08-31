<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface DistrictRepository.
 *
 * @package namespace App\Repositories;
 */
interface DistrictRepository extends RepositoryInterface
{
    public function listDistrictByProvince(array $filter): LengthAwarePaginator;
}
