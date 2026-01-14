<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $createRoleAdmin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $createRoleDoctor = Role::create(['name' => 'doctor', 'guard_name' => 'web']);
        $createRolePharmacist = Role::create(['name' => 'pharmacist', 'guard_name' => 'web']);

        $createUserAdmin = User::create([
            'name'              => 'Admin',
            'email'             => 'admin@email.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $createUserAdmin->assignRole($createRoleAdmin);
    }
}
