<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PermissionRole;

class PermissionRoleController extends Controller
{
    public function __construct()
    {
        parent::__construct(new PermissionRole());
    }
}
