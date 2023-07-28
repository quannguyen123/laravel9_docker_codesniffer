<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface TagRepositoryRepository.
 *
 * @package namespace App\Repositories;
 */
interface TagRepository extends RepositoryInterface
{
    public function search(array $filter): LengthAwarePaginator;
    public function publicSearchTag(array $filter): LengthAwarePaginator;
}
