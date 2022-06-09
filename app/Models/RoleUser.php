<?php
namespace App\Models;

use App\Models\Contracts\IModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model implements IModel
{

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = ['role_id', 'user_id'];

    /**
     * Related models definitions
     *
     * @var array
     */
    public $relation_methods = ['user', 'role'];

    public function getMigrateKey()
    {
        return $this->getForeignKey();
    }


    
    public function rules()
    {
        return [
            'role_id' => 'required|exists:roles,id',
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function update_rules()
    {
        return [
            'role_id' => 'sometimes|exists:roles,id',
            'user_id' => 'sometimes|exists:users,id',
            "id" => "required|exists:".$this->getTable().",$this->primaryKey"
        ];
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
