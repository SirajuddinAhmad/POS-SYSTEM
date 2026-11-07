<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Only admin can view users list
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak: Hanya Administrator yang dapat melihat daftar pengguna.');
        }

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Akses ditolak: Hanya Administrator yang dapat menambah pengguna.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,cashier',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('swal_success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $currentUser = Auth::user();
        $targetUser = User::findOrFail($id);

        // Kasir Tidak bisa Hapus & Edit Administrator
        if ($currentUser->isCashier() && $targetUser->isAdmin()) {
            abort(403, 'Kasir dilarang mengedit data Administrator!');
        }

        // Kasir Bisa Edit Profile pribadi namun tidak bisa untuk kasir lain
        if ($currentUser->isCashier() && $currentUser->id !== $targetUser->id) {
            abort(403, 'Kasir hanya bisa mengedit profil pribadi, tidak bisa mengedit akun kasir lain.');
        }

        return view('users.edit', compact('targetUser'));
    }

    public function update(Request $request, $id)
    {
        $currentUser = Auth::user();
        $targetUser = User::findOrFail($id);

        // RULE: Kasir Tidak bisa Hapus & Edit Administrator
        if ($currentUser->isCashier() && $targetUser->isAdmin()) {
            abort(403, 'Akses ditolak: Kasir tidak diizinkan mengedit akun Administrator!');
        }

        // RULE: Kasir Bisa Edit Profile pribadi namun tidak bisa untuk kasir lain
        if ($currentUser->isCashier() && $currentUser->id !== $targetUser->id) {
            abort(403, 'Akses ditolak: Kasir hanya bisa mengedit profil pribadi dan tidak bisa mengedit kasir lain.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($targetUser->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($targetUser->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'role' => $currentUser->isAdmin() ? 'required|in:admin,cashier' : 'nullable',
            'status' => $currentUser->isAdmin() ? 'required|in:active,inactive' : 'nullable',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // If cashier is updating, keep existing role and status
        if ($currentUser->isCashier()) {
            unset($validated['role'], $validated['status']);
        }

        if ($request->hasFile('avatar')) {
            if ($targetUser->avatar && Storage::disk('public')->exists($targetUser->avatar)) {
                Storage::disk('public')->delete($targetUser->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $targetUser->update($validated);

        if ($currentUser->isCashier()) {
            return redirect()->route('profile')->with('swal_success', 'Profil pribadi Anda berhasil diperbarui!');
        }

        return redirect()->route('users.index')->with('swal_success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $currentUser = Auth::user();
        $targetUser = User::findOrFail($id);

        // RULE: Kasir Tidak bisa Hapus & Edit Administrator
        if ($currentUser->isCashier()) {
            abort(403, 'Akses ditolak: Kasir dilarang menghapus akun apapun!');
        }

        // Admin cannot delete themselves
        if ($currentUser->id === $targetUser->id) {
            return redirect()->route('users.index')->with('swal_error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login!');
        }

        if ($targetUser->avatar && Storage::disk('public')->exists($targetUser->avatar)) {
            Storage::disk('public')->delete($targetUser->avatar);
        }

        $targetUser->delete();

        return redirect()->route('users.index')->with('swal_success', 'Pengguna berhasil dihapus!');
    }

    // Kasir & Admin Personal Profile Page
    public function profile()
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
            }
            $user->password = Hash::make($request->new_password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->save();

        return back()->with('swal_success', 'Profil Anda berhasil diperbarui!');
    }
}
