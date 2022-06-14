<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PermissionUser;

class PermisionUserController extends Controller
{
    public function __construct()
    {
        parent::__construct(new PermissionUser());
    }
}
