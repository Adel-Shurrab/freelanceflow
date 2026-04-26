<?php

namespace App\Services;

use App\Contracts\Repositories\BaseRepositoryInterface;

abstract class BaseService
{
    public function __construct(protected BaseRepositoryInterface $repository) {}
}
