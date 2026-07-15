<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index()
    {
        $users = User::orderByRaw("FIELD(role, 'camat', 'petugas', 'masyarakat')")
            ->orderBy('name')
            ->get();

        return view('dashboard.users', compact('users'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^(08|628)[0-9]{8,13}$/'],
            'nip' => ['nullable', 'string', 'max:18', 'regex:/^[0-9]+$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:masyarakat,petugas,camat',
        ], [
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, dan titik.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'phone.regex' => 'Nomor telepon tidak valid (contoh: 08xxxxxxxxxx).',
            'nip.regex' => 'NIP hanya boleh mengandung angka.',
            'nip.max' => 'NIP maksimal 18 karakter.',
            'role.in' => 'Role yang dipilih tidak valid.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nip' => $request->nip,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent editing own account via this panel
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Gunakan halaman Profil untuk mengedit akun Anda sendiri.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^(08|628)[0-9]{8,13}$/'],
            'nip' => ['nullable', 'string', 'max:18', 'regex:/^[0-9]+$/'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'is_active' => 'nullable|boolean',
        ], [
            'name.regex' => 'Nama hanya boleh mengandung huruf, spasi, dan titik.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'phone.regex' => 'Nomor telepon tidak valid (contoh: 08xxxxxxxxxx).',
            'nip.regex' => 'NIP hanya boleh mengandung angka.',
            'nip.max' => 'NIP maksimal 18 karakter.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nip' => $request->nip,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // is_active toggle (only for camat accounts)
        if ($user->role === 'camat') {
            $data['is_active'] = $request->boolean('is_active', $user->is_active);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Prevent deleting camat that has signed documents
        if ($user->role === 'camat') {
            $hasSignedDocs = \App\Models\Permohonan::whereIn('status', ['ditandatangani', 'selesai'])
                ->whereHas('logs', function ($q) use ($user) {
                    $q->where('actor_id', $user->id)
                      ->where('action', 'signed_camat');
                })
                ->exists();

            if ($hasSignedDocs) {
                return redirect()->back()->with('error', 'Camat ini memiliki dokumen yang sudah ditandatangani. Gunakan fitur nonaktifkan akun sebagai gantinya.');
            }
        }

        $user->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
