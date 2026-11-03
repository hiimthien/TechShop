<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ApiResponseTrait;

abstract class Controller
{
    use ApiResponseTrait;
}
