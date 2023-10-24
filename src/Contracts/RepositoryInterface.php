<?php

declare(strict_types=1);

namespace Juling\Generator\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function model(): Model;
}
