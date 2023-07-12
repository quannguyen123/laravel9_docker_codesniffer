<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface WelfareRepository.
 *
 * @package namespace App\Repositories;
 */
interface WelfareRepository extends RepositoryInterface
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
