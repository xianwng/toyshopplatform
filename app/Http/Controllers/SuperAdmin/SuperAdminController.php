<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $totalAdmins = User::where('role', 'admin')->count();
        $activeAdmins = User::where('role', 'admin')->where('is_active', true)->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalSuperAdmins = User::where('role', 'super_admin')->count();

        return view('super-admin.dashboard', compact(
            'totalAdmins', 
            'activeAdmins',
            'totalCustomers',
            'totalSuperAdmins'
        ));
    }

    public function showAdmins(Request $request)
    {
        $query = User::where('role', 'admin');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $admins = $query->latest()->paginate(10);

        return view('super-admin.admins.index', compact('admins'));
    }

    public function createAdmin()
    {
        return view('super-admin.admins.create');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|unique:users|min:3|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('super-admin.admins.index')->with('success', 'Admin created successfully.');
    }

    public function editAdmin(User $admin)
    {
        if (!$admin->isAdmin()) {
            abort(404);
        }

        return view('super-admin.admins.edit', compact('admin'));
    }

    public function updateAdmin(Request $request, User $admin)
    {
        if (!$admin->isAdmin()) {
            abort(404);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'username' => 'required|string|max:255|unique:users,username,' . $admin->id,
        ]);

        $admin->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('super-admin.admins.index')->with('success', 'Admin updated successfully.');
    }

    public function toggleAdminStatus(User $admin)
    {
        if (!$admin->isAdmin()) {
            abort(404);
        }

        $admin->update([
            'is_active' => !$admin->is_active,
        ]);

        $status = $admin->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Admin {$status} successfully.");
    }

    public function destroyAdmin(User $admin)
    {
        if (!$admin->isAdmin()) {
            abort(404);
        }

        $admin->delete();
        return back()->with('success', 'Admin deleted successfully.');
    }

    // Super Admin Management Methods
    public function showSuperAdmins(Request $request)
    {
        $query = User::where('role', 'super_admin');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $superAdmins = $query->latest()->paginate(10);

        return view('super-admin.super-admins.index', compact('superAdmins'));
    }

    public function showCreateSuperAdmin()
    {
        return view('super-admin.super-admins.create');
    }

    public function storeSuperAdmin(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|unique:users|min:3|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('super-admin.super-admins.index')->with('success', 'Super Admin created successfully.');
    }

    public function editSuperAdmin($id)
    {
        $superAdmin = User::where('role', 'super_admin')->findOrFail($id);
        return view('super-admin.super-admins.edit', compact('superAdmin'));
    }

    public function updateSuperAdmin(Request $request, $id)
    {
        $superAdmin = User::where('role', 'super_admin')->findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $superAdmin->id,
            'username' => 'required|string|max:255|unique:users,username,' . $superAdmin->id,
        ]);

        $superAdmin->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('super-admin.super-admins.index')->with('success', 'Super Admin updated successfully.');
    }

    public function toggleSuperAdminStatus($id)
    {
        $superAdmin = User::where('role', 'super_admin')->findOrFail($id);

        // Prevent self-deactivation
        if ($superAdmin->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $superAdmin->update([
            'is_active' => !$superAdmin->is_active,
        ]);

        $status = $superAdmin->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Super Admin {$status} successfully.");
    }

    public function destroySuperAdmin($id)
    {
        $superAdmin = User::where('role', 'super_admin')->findOrFail($id);

        // Prevent self-deletion
        if ($superAdmin->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $superAdmin->delete();
        return back()->with('success', 'Super Admin deleted successfully.');
    }
}