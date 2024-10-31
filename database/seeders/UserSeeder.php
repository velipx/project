<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Generišemo 100 nasumičnih korisnika i dodeljujemo im ulogu "user"
        User::factory(100000)->create()->each(function ($user) use ($userRole) {
            $user->assignRole($userRole);
        });
    }
}
