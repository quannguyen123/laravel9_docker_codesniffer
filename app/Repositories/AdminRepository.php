<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface AdminRepository.
 *
 * @package namespace App\Repositories;
 */
interface AdminRepository extends RepositoryInterface
{
    public function search(array $filter): LengthAwarePaginator;
}
