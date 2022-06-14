<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        parent::__construct(new Permission());
    }
}
