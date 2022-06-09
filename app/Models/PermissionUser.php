<?php
namespace App\Models;

use App\Models\Contracts\IModel;
use Illuminate\Database\Eloquent\Model;

class PermissionUser extends Model implements IModel
{

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = ['permission_id', 'user_id'];

    /**
     * Related models definitions
     *
     * @var array
     */
    public $relation_methods = ['permission', 'user'];

    public function getMigrateKey()
    {
        return $this->getForeignKey();
    }


    

    public function rules()
    {
        return [
            'permission_id' => 'required|exists:permissions,id',
            'user_id' => 'required|exists:users,id',
        ];
    }

    public function update_rules()
    {
        return [
            'permission_id' => 'sometimes|exists:permissions,id',
            'user_id' => 'sometimes|exists:users,id',
            "id" => "required|exists:".$this->getTable().",$this->primaryKey"
        ];
    }
    
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
