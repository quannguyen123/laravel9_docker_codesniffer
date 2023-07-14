<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ServiceRepository.
 *
 * @package namespace App\Repositories;
 */
interface ServiceRepository extends RepositoryInterface
{
    public function search(array $filter): LengthAwarePaginator;
}
