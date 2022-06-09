<?php

namespace App\Models;

use App\Models\Contracts\IModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model implements IModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'label',
        'display_label',
    ];

    /**
     * Related models definitions
     *
     * @var array
     */
    public $relation_methods = ['users', 'roles'];

    public function getMigrateKey()
    {
        return $this->getForeignKey();
    }


    
    public function rules()
    {
        return [
            'label' => 'required'
        ];
    }

    public function update_rules()
    {
        return [
            'label' => 'sometimes',
            "id" => "required|exists:".$this->getTable().",$this->primaryKey"
        ];
    }

    public function roles()
    {
        return $this->hasMany(Role::class, 'role_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'user_id');
    }
}
