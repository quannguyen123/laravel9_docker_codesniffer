<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ProvinceRepository.
 *
 * @package namespace App\Repositories;
 */
interface ProvinceRepository extends RepositoryInterface
{
    public function listProvince(array $filter): LengthAwarePaginator;

}
