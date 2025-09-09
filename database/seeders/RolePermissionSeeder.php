<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ======== BUAT PERMISSIONS =========
        $permissions = [
            'manage users',
            'manage articles',
            'view reports',
            'write articles',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ======== BUAT ROLES =========
        $roles = [
            'super-admin' => $permissions, // semua permission
            'admin'       => ['manage users', 'manage articles', 'view reports'],
            'operator'    => ['write articles'],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($perms);
        }

        // ======== ASSIGN ROLE KE USER =========
        // Contoh user id 1 = super-admin
        $superAdmin = User::find(1);
        if ($superAdmin) {
            $superAdmin->assignRole('super-admin');
        }

        // Contoh user id 2 = admin
        $admin = User::find(2);
        if ($admin) {
            $admin->assignRole('admin');
        }

        // Contoh user id 3 = operator
        $operator = User::find(3);
        if ($operator) {
            $operator->assignRole('operator');
        }

        $this->command->info('Roles & Permissions berhasil dibuat dan di-assign ke user!');
    }
}
