<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface JobRepository.
 *
 * @package namespace App\Repositories;
 */
interface JobRepository extends RepositoryInterface
{
    public function search(array $filter): LengthAwarePaginator;
    public function getJobByStatus(array $filter): LengthAwarePaginator;
}
