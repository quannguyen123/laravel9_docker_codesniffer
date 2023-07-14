<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface JobTitleRepository.
 *
 * @package namespace App\Repositories;
 */
interface JobTitleRepository extends RepositoryInterface
{
    public function search(array $filter): LengthAwarePaginator;
}
