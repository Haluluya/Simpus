<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $roleFilter = $request->query('role');

        $users = User::query()
            ->with('roles:id,name')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter, function ($query) use ($roleFilter) {
                $query->whereHas('roles', function ($q) use ($roleFilter) {
                    $q->where('name', $roleFilter);
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::query()->orderBy('name')->get();
        
        // Count statistics
        $totalUsers = User::count();
        $activeUsers = User::whereNotNull('email_verified_at')->count();
        $inactiveUsers = User::whereNull('email_verified_at')->count();
        $todayLogins = User::whereDate('last_login_at', today())->count();

        return view('users.index', [
            'users' => $users,
            'roles' => $roles,
            'search' => $search,
            'roleFilter' => $roleFilter,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'todayLogins' => $todayLogins,
        ]);
    }

    public function edit(User $user)
    {
        $roles = Role::query()->orderBy('name')->pluck('name');

        return view('users.edit', [
            'user' => $user->load('roles:id,name'),
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => [
                'string',
                Rule::exists('roles', 'name'),
            ],
        ]);

        $selectedRoles = array_values(array_unique($data['roles']));

        if ($user->id === $request->user()->id && ! in_array('administrator', $selectedRoles, true)) {
            return back()
                ->withErrors(['roles' => __('Anda tidak dapat menghapus peran administrator dari akun Anda sendiri.')])
                ->withInput();
        }

        if ($user->hasRole('administrator') && ! in_array('administrator', $selectedRoles, true)) {
            $otherAdmins = User::role('administrator')
                ->whereKeyNot($user->id)
                ->exists();

            if (! $otherAdmins) {
                return back()
                    ->withErrors(['roles' => __('Setidaknya harus ada satu administrator aktif.')])
                    ->withInput();
            }
        }

        $user->syncRoles($selectedRoles);

        return redirect()
            ->route('users.index')
            ->with('success', __('Peran pengguna berhasil diperbarui.'));
    }

    public function create()
    {
        $roles = Role::query()->orderBy('name')->get();

        return view('users.create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nik' => ['required', 'string', 'max:20', 'unique:users,nik'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'nik' => $data['nik'],
            'password' => bcrypt($data['password']),
            'email_verified_at' => ($data['is_active'] ?? true) ? now() : null,
        ]);

        $user->syncRoles($data['roles']);

        return redirect()
            ->route('users.index')
            ->with('success', __('User berhasil ditambahkan.'));
    }

    public function resetPassword(Request $request, User $user)
    {
        $user->update([
            'password' => bcrypt('password123'),
        ]);

        return back()->with('success', __('Password berhasil direset menjadi: password123'));
    }

    public function toggleStatus(User $user)
    {
        // Toggle status akun (aktif/nonaktif)
        $isCurrentlyActive = !is_null($user->email_verified_at);
        $newStatus = $isCurrentlyActive ? null : now();
        
        $user->email_verified_at = $newStatus;
        $user->save();

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('users.index')->with('success', "User {$user->name} berhasil {$statusText}.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['error' => __('Anda tidak dapat menghapus akun Anda sendiri.')]);
        }

        if ($user->hasRole('administrator')) {
            $otherAdmins = User::role('administrator')->whereKeyNot($user->id)->exists();
            if (!$otherAdmins) {
                return back()->withErrors(['error' => __('Tidak dapat menghapus administrator terakhir.')]);
            }
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', __('User berhasil dihapus.'));
    }
}

