<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OccupationRepository.
 *
 * @package namespace App\Repositories;
 */
interface OccupationRepository extends RepositoryInterface
{
    /**
     * Get List and Filter Admin
     *
     * @param array $filter
     *
     * @return LengthAwarePaginator
     */
    public function search(array $filter): LengthAwarePaginator;
}
