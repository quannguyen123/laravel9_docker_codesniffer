<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface PartnerRepository.
 *
 * @package namespace App\Repositories;
 */
interface PartnerRepository extends RepositoryInterface
{
    public function search(array $filter): LengthAwarePaginator;
}
