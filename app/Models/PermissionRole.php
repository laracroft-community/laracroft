<?php
namespace App\Models;

use App\Models\Contracts\IModel;
use App\Rules\Uniques;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class PermissionRole extends Model implements IModel
{

    /**
     * Fillable column of the related table
     *
     * @var array
     */
    protected $fillable = ['permission_id', 'role_id'];

    /**
     * Related models definitions
     *
     * @var array
     */
    public $relation_methods = ['permission', 'role'];
    
    public function getMigrateKey()
    {
        return $this->getForeignKey();
    }
    

    public function rules()
    {
        return [
            'permission_id' => 'bail|required|exists:permissions,id',
            // 'role_id' => ['bail','required', 'exists:roles,id', new Uniques($this->getTable(), ['role_id', 'permission_id'], $this->request)],
            'role_id' => 'bail|required|exists:roles,id|unique:role_id,'.$this->request,
        ];
    }

    public function update_rules()
    {
        return [
            'permission_id' => 'sometimes|exists:permissions,id',
            'role_id' => 'sometimes|exists:roles,id',
            "id" => "required|exists:".$this->getTable().",$this->primaryKey"
        ];
    }

    // relations

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
