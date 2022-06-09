<?php 

namespace App\Models\Contracts;

use Illuminate\Http\Request;

interface IModel {
    
    const IGNORE_RULE = 'ignore';

    public function rules();
    
    public function update_rules();

    public function getMigrateKey();
}
