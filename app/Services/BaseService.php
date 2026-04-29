<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\BaseRepositoryInterface;

abstract class BaseService
{
    public function __construct(protected BaseRepositoryInterface $repository) {}
}
