<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasController extends Controller
{
    public function index()
    {
        // FCFS: Ordered by Queue Number (which is time based)
        $permohonans = Permohonan::with('user')
            ->where('status', 'pending')
            ->orderBy('no_antrean', 'asc')
            ->get();

        // Items signed by Camat, waiting for petugas to add nomor surat
        $ditandatangani = Permohonan::with('user')
            ->where('status', 'ditandatangani')
            ->orderBy('updated_at', 'asc')
            ->get();

        return view('dashboard.petugas', compact('permohonans', 'ditandatangani'));
    }

    public function validateBerkas(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'required_if:action,reject',
        ]);

        $permohonan = Permohonan::findOrFail($id);

        // Strict FCFS check: Ensure this is the first in queue
        $firstInQueue = Permohonan::where('status', 'pending')->orderBy('no_antrean', 'asc')->first();
        if ($firstInQueue && $firstInQueue->id !== $permohonan->id) {
            return redirect()->back()->with('error', 'Harap proses permohonan sesuai urutan antrean! Nomor ' . $firstInQueue->no_antrean . ' harus diproses dulu.');
        }

        if ($request->action === 'reject') {
            $permohonan->update([
                'status' => 'ditolak',
                'keterangan' => $request->keterangan,
            ]);
            $permohonan->logs()->create([
                'action' => 'rejected',
                'actor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Permohonan ditolak.');
        } else {
            // Approve / Ajukan ke Camat (tanpa nomor surat)
            $permohonan->update([
                'status' => 'menunggu_camat',
            ]);
            $permohonan->logs()->create([
                'action' => 'approved_petugas',
                'actor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Berkas valid. Diajukan ke Camat untuk ditandatangani.');
        }
    }

    /**
     * Add nomor surat after Camat has signed the document.
     */
    public function nomorSurat(Request $request, $id)
    {
        $request->validate([
            'nomor_surat' => 'required|string',
        ]);

        $permohonan = Permohonan::findOrFail($id);

        if ($permohonan->status !== 'ditandatangani') {
            return redirect()->back()->with('error', 'Permohonan belum ditandatangani oleh Camat.');
        }

        $permohonan->update([
            'status' => 'selesai',
            'nomor_surat' => $request->nomor_surat,
        ]);
        $permohonan->logs()->create([
            'action' => 'numbered',
            'actor_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Nomor Surat berhasil ditambahkan: ' . $request->nomor_surat . '. Berkas siap diterima masyarakat.');
    }
}
