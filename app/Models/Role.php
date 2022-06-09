<?php
namespace App\Models;

use App\Models\Contracts\IModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model implements IModel
{

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = ['label', 'display_label'];

    /**
     * Related models definitions
     *
     * @var array
     */
    public $relation_methods = ['permissions', 'users', 'permission_roles'];

    public function getMigrateKey()
    {
        return $this->getForeignKey();
    }


    

    public function rules()
    {
        return [
            'label' => 'required',
        ];
    }

    public function update_rules()
    {
        return [
            'label' => 'sometimes',
            "id" => "required|exists:".$this->getTable().",$this->primaryKey"
        ];
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_users', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'permission_roles', 'role_id');
    }

    public function permission_roles()
    {
        return $this->hasMany(PermissionRole::class);
    }
}
