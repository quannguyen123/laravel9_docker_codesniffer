<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface AlertJobRepository.
 *
 * @package namespace App\Repositories;
 */
interface AlertJobRepository extends RepositoryInterface
{
    public function search(array $filter): LengthAwarePaginator;
}
