<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users', [
            'users' => User::with('roles', 'permissions')
                ->latest()
                ->get()
                ->map(function ($user) {
                    return [
                        'id'             => $user->id,
                        'name'           => $user->name,
                        'email'          => $user->email,
                        'username'       => $user->username,
                        'roles'          => $user->getRoleNames(),
                        'permissions'    => $user->getPermissionNames(),
                        'is_active'      => $user->is_active,
                        'trusted_writer' => $user->trusted_writer,
                        'created_at'     => $user->created_at?->toDateString(),
                    ];
                }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'username'       => 'required|string|max:255|unique:users,username',
            'password'       => 'required|string|min:8',
            'roles'          => 'required|array',
            'roles.*'        => 'string|exists:roles,name',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'string|exists:permissions,name',
            'is_active'      => 'required|boolean',
            'trusted_writer' => 'boolean',
        ]);

        $user = User::create([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'username'       => $validated['username'],
            'password'       => Hash::make($validated['password']),
            'is_active'      => $validated['is_active'],
            'trusted_writer' => $validated['trusted_writer'] ?? false,
        ]);

        $user->assignRole($validated['roles']);
        if (!empty($validated['permissions'])) {
            $user->givePermissionTo($validated['permissions']);
        }

        return redirect()
            ->route('admin.users')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $user->id,
            'username'       => 'required|string|max:255|unique:users,username,' . $user->id,
            'roles'          => 'required|array',
            'roles.*'        => 'string|exists:roles,name',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'string|exists:permissions,name',
            'is_active'      => 'required|boolean',
            'trusted_writer' => 'required|boolean',
            'password'       => ['sometimes', 'nullable', 'string', 'min:8'],
        ]);

        $data = [
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'username'       => $validated['username'],
            'is_active'      => $validated['is_active'],
            'trusted_writer' => $validated['trusted_writer'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        $user->syncRoles($validated['roles']);
        $user->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users')
            ->with('success', 'User berhasil dihapus (tersimpan di trash).');
    }

    public function trashed()
    {
        $users = User::onlyTrashed()->latest()->get();

        return Inertia::render('Admin/Users/Trashed', [
            'users' => $users,
        ]);
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()
            ->route('admin.users.trashed')
            ->with('success', 'User berhasil direstore.');
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()
            ->route('admin.users.trashed')
            ->with('success', 'User dihapus permanen.');
    }

    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Status user berhasil diperbarui.');
    }

    public function toggleTrusted(User $user)
    {
        $user->trusted_writer = !$user->trusted_writer;
        $user->save();

        return back()->with('success', 'Status Trusted Writer berhasil diperbarui.');
    }

   public function bulkDestroy(Request $request)
{
    $ids = $request->ids;

    if (!$ids || !is_array($ids)) {
        return back()->withErrors(['message' => 'Tidak ada user dipilih']);
    }

    User::whereIn('id', $ids)->delete(); // ini soft delete kalau pakai SoftDeletes

    return back()->with('success', count($ids).' user berhasil dihapus!');
}


  public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            User::onlyTrashed()->whereIn('id', $ids)->restore();
        }

        return back()->with('success', count($ids).' user berhasil direstore.');
    }

    // 🔹 Bulk Force Delete
    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            User::onlyTrashed()->whereIn('id', $ids)->forceDelete();
        }

        return back()->with('success', count($ids).' user berhasil dihapus permanen.');
    }

}
