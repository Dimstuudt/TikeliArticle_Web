<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends Controller
{
    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/UserPermissions', [
            'user' => $user->load('permissions'),
            'permissions' => Permission::pluck('name'),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $user->syncPermissions($request->permissions);

        return redirect()->route('admin.users')->with('success', 'Permissions berhasil diperbarui');
    }
}
