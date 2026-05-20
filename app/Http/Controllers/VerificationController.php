<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;

class VerificationController extends Controller
{
    public function verify($token)
    {
        $permohonan = Permohonan::where('verification_token', $token)->first();

        if (!$permohonan) {
            return view('verification.invalid');
        }

        // Fetch the Camat user
        $camat = \App\Models\User::where('role', 'camat')->first();

        return view('verification.valid', compact('permohonan', 'camat'));
    }
}
