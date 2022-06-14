<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RoleUser;

class RoleUserController extends Controller
{
    public function __construct()
    {
        parent::__construct(new RoleUser());
    }
}
