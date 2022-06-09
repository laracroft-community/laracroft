<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'username' => 'bob',
            'email' => 'bob@bob.bob',
            'password' => Hash::make('123456'),
        ]);

        $permissions = [
            ["label" => "list-permissions", "display_label" => "list-permissions"],
            ["label" => "create-permissions", "display_label" => "create-permissions"],
            ["label" => "update-permissions", "display_label" => "update-permissions"],
            ["label" => "delete-permissions", "display_label" => "delete-permissions"],
            ["label" => "list-roles", "display_label" => "list-roles"],
            ["label" => "create-roles", "display_label" => "create-roles"],
            ["label" => "update-roles", "display_label" => "update-roles"],
            ["label" => "delete-roles", "display_label" => "delete-roles"],
            ["label" => "list-users", "display_label" => "list-users"],
            ["label" => "create-users", "display_label" => "create-users"],
            ["label" => "update-users", "display_label" => "update-users"],
            ["label" => "delete-users", "display_label" => "delete-users"],
        ];

        foreach ($permissions as $value) {
            $permission = Permission::create($value);
            PermissionUser::create(
                array_merge(['user_id' => $user->id, 'permission_id' => $permission->id])
            );
        }
    }
}
