<?php

namespace App\Models;

use App\Models\Contracts\IModel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements IModel
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'firstname',
        'lastname',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $relation_methods = ['permissions', 'roles'];

    public function getMigrateKey()
    {
        return $this->getForeignKey();
    }

    public function rules()
    {
        return [
            'username' => ['required', 'unique:'.$this->getTable()],
            'email' => ['required', 'email', 'unique:'.$this->getTable()],
            'password' => ['required', 'confirmed'],
        ];
    }

    public function update_rules()
    {
        return [
            'username' => ['sometimes', IModel::IGNORE_RULE],
            'email' => ['sometimes', 'email', IModel::IGNORE_RULE],
            'password' => 'sometimes',
            "id" => "required|exists:".$this->getTable().",$this->primaryKey"
        ];
    }

    // relations
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_users', 'user_id');
    }

    /**
     * check if current user has a specific permission
     * 
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return $this->permissions()->where('label', $permission)->first() !== null;
    }

}
