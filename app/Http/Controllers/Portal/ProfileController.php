<?php

namespace App\Http\Controllers\Portal; // <- Namespace sudah benar

use App\Http\Controllers\Controller; // <- Import Controller utama
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan form profil pengguna.
     */
    public function edit(Request $request, string $subdomain): View
    {
        $desa = app('tenant');

        return view('portal.profile.edit', [
            'user' => $request->user(),
            'subdomain' => $subdomain,
            'desa' => $desa,
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna.
     */
    public function update(string $subdomain, ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Redirect kembali ke halaman edit profil di portal
        return Redirect::route('portal.profile.edit', ['subdomain' => $subdomain])
                       ->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request, string $subdomain): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        // Redirect kembali ke halaman edit profil di portal
        return Redirect::route('portal.profile.edit', ['subdomain' => $subdomain])
                       ->with('status', 'password-updated');
    }

    /**
     * Menghapus akun pengguna.
     */
    public function destroy(Request $request, string $subdomain): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan ke halaman utama setelah hapus akun
        return Redirect::to('/');
    }

    // Untuk method destroy, kita tidak perlukan di portal agar lebih aman.
    // User tidak sengaja menghapus akunnya sendiri.
}