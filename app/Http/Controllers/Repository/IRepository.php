<?php 

namespace App\Http\Controllers\Repository;

use Illuminate\Http\Request;

interface IRepository {
    
    public function rules();
    
    public function update_rules();

    public function setModel($model);
}
