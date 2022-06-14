<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        parent::__construct(new Role());
    }
}
