<?php
// database/seeders/SyncUserRolesSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class SyncUserRolesSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->role === 'admin') {
                $user->syncRoles(['admin']);
            } elseif ($user->role === 'operator') {
                $user->syncRoles(['operator']);
            } elseif ($user->role === 'super-admin') {
                $user->syncRoles(['super-admin']);
            }
        }
    }
}

