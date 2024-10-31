<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Grupe permisija
        $permissions = [
            // Permisije za korisnike
            'view users',
            'create users',
            'edit users',
            'delete users',
        ];

        // Kreiraj sve permisije ako ne postoje
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Kreiraj role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Dodeli permisije admin roli
        $adminRole->givePermissionTo($permissions); // Admin ima sve permisije

        // Dodeli osnovne permisije korisniku
        $userRole->givePermissionTo([
            'view users',
        ]);

        $user = User::find(1);
        $user->assignRole('admin');
    }
}
