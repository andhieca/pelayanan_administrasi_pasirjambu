<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use App\Services\FonnteService;
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
            'keterangan' => 'nullable|string|max:500',
            'invalid_items' => 'nullable|array',
            'invalid_items.*' => 'string'
        ], [
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
        ]);

        $permohonan = Permohonan::findOrFail($id);

        // Strict FCFS check: Ensure this is the first in queue
        $firstInQueue = Permohonan::where('status', 'pending')->orderBy('no_antrean', 'asc')->first();
        if ($firstInQueue && $firstInQueue->id !== $permohonan->id) {
            return redirect()->back()->with('error', 'Harap proses permohonan sesuai urutan antrean! Nomor ' . $firstInQueue->no_antrean . ' harus diproses dulu.');
        }

        if ($request->action === 'reject') {
            $invalidItems = $request->invalid_items;
            if (empty($invalidItems) && $request->filled('invalid_items_json')) {
                $invalidItems = json_decode($request->invalid_items_json, true);
            }
            if (is_string($invalidItems)) {
                $invalidItems = json_decode($invalidItems, true);
            }

            $permohonan->update([
                'status' => 'ditolak',
                'keterangan' => $request->keterangan,
                'invalid_items' => is_array($invalidItems) ? array_values(array_filter($invalidItems)) : [],
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
            'nomor_surat' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9\s\/\.\-]+$/'],
        ], [
            'nomor_surat.regex' => 'Nomor surat hanya boleh mengandung huruf, angka, spasi, garis miring, titik, dan strip.',
            'nomor_surat.max' => 'Nomor surat maksimal 100 karakter.',
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

        // Kirim notifikasi WhatsApp ke masyarakat
        $this->sendWhatsAppNotification($permohonan);

        return redirect()->back()->with('success', 'Nomor Surat berhasil ditambahkan: ' . $request->nomor_surat . '. Berkas siap diterima masyarakat.');
    }

    /**
     * Send WhatsApp notification to masyarakat via Fonnte.
     */
    protected function sendWhatsAppNotification(Permohonan $permohonan): void
    {
        // Get phone number: prioritize metadata.whatsapp, fallback to user.phone
        $phone = $permohonan->metadata['whatsapp'] ?? null;

        if (empty($phone)) {
            $permohonan->loadMissing('user');
            $phone = $permohonan->user->phone ?? null;
        }

        if (empty($phone)) {
            return; // No phone number available, skip notification
        }

        $nama = $permohonan->user->name ?? 'Bapak/Ibu';
        $jenisLayanan = $permohonan->jenis_layanan;
        $nomorSurat = $permohonan->nomor_surat;

        $message = "Assalamu'alaikum {$nama},\n\n"
            . "Surat *{$jenisLayanan}* Anda telah selesai diproses.\n\n"
            . "📄 Nomor Surat: *{$nomorSurat}*\n"
            . "📋 Jenis: {$jenisLayanan}\n\n"
            . "Surat Anda telah selesai, untuk mendownloadnya silakan login ke akun Anda masing-masing di website Pelayanan Administrasi Kecamatan Pasirjambu.\n\n"
            . "Terima kasih.\n"
            . "— Pelayanan Administrasi Kecamatan Pasirjambu";

        $fonnte = new FonnteService();
        $fonnte->sendMessage($phone, $message);
    }
}
