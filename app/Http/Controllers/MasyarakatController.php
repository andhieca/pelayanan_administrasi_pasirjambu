<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MasyarakatController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $permohonans = Permohonan::where('user_id', $userId)
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();

        $drafts = Permohonan::where('user_id', $userId)
            ->where('status', 'draft')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.masyarakat', compact('permohonans', 'drafts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_layanan' => 'required',
            'berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->jenis_layanan == 'Dispen Nikah' && !$request->has('is_draft')) {
            $request->validate([
                'suami.nama' => 'required|string',
                'suami.nik' => 'required|numeric',
                'suami.bin' => 'required|string',
                'suami.ttl' => 'required|string',
                'suami.agama' => 'required|string',
                'suami.pekerjaan' => 'required|string',
                'suami.status' => 'required|string',
                'suami.alamat' => 'required|string',

                'istri.nama' => 'required|string',
                'istri.nik' => 'required|numeric',
                'istri.binti' => 'required|string',
                'istri.ttl' => 'required|string',
                'istri.agama' => 'required|string',
                'istri.pekerjaan' => 'required|string',
                'istri.status' => 'required|string',
                'istri.alamat' => 'required|string',

                'pernikahan.hari' => 'required|string',
                'pernikahan.tanggal' => 'required|date',
                'pernikahan.waktu' => 'required|string',
                'pernikahan.tempat' => 'required|string',

                'alasan' => 'required|string',
                'whatsapp' => 'required|numeric',

                'files.ktp_suami' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.ktp_istri' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.kk_suami' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.kk_istri' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.pas_foto' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.n1_istri' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.n1_suami' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.n2' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.n4' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.n10' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
        }

        if ($request->jenis_layanan == 'Izin Keramaian' && !$request->has('is_draft')) {
            $request->validate([
                'pemohon.nama' => 'required|string',
                'pemohon.ttl' => 'required|string',
                'pemohon.gender' => 'required|string',
                'pemohon.nik' => 'required|numeric',
                'pemohon.pekerjaan' => 'required|string',
                'pemohon.alamat' => 'required|string',
                'keramaian.tanggal' => 'required|string',
                'keramaian.acara' => 'required|string',
                'keramaian.lokasi' => 'required|string',
                'keramaian.hiburan' => 'required|string',
                'files.ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.proposal_acara' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
        }

        if ($request->jenis_layanan == 'Rekomendasi Bantuan' && !$request->has('is_draft')) {
            $request->validate([
                'rekomendasi.jenis_kelompok' => 'required|string',
                'rekomendasi.nama_kelompok' => 'required|string',
                'rekomendasi.alamat' => 'required|string',
                'rekomendasi.perihal' => 'required|string',
                'rekomendasi.nama_desa' => 'required|string',
                'files.proposal' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
        }

        $is_draft = $request->has('is_draft') && $request->is_draft;
        $status = $is_draft ? 'draft' : 'pending';
        $no_antrean = null;

        if (!$is_draft) {
            $logic_number = Permohonan::whereDate('created_at', today())->whereNotNull('no_antrean')->count() + 1;
            $no_antrean = date('Ymd') . str_pad($logic_number, 3, '0', STR_PAD_LEFT);
        }

        $path = null;
        if ($request->hasFile('berkas')) {
            $path = $request->file('berkas')->store('berkas_permohonan', 'public');
        }

        $metadata = null;
        if ($request->jenis_layanan == 'Dispen Nikah') {
            $metadata = [
                'suami' => $request->suami,
                'istri' => $request->istri,
                'pernikahan' => $request->pernikahan,
                'alasan' => $request->alasan,
                'whatsapp' => $request->whatsapp,
                'files' => [],
            ];

            // Handle multi-file uploads
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    $metadata['files'][$key] = $file->store('berkas_dispen_nikah', 'public');
                }
            }

            // For backward compatibility or list view, use one main file as 'file_path'
            // We can use pas_foto or ktp as the 'main' file path if needed, or leave it null/generic
            if (isset($metadata['files']['pas_foto'])) {
                $path = $metadata['files']['pas_foto'];
            }
        }

        if ($request->jenis_layanan == 'Izin Keramaian') {
            $metadata = [
                'pemohon' => $request->pemohon,
                'keramaian' => $request->keramaian,
                'files' => [],
            ];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    $metadata['files'][$key] = $file->store('berkas_izin_keramaian', 'public');
                }
            }

            if (isset($metadata['files']['ktp'])) {
                $path = $metadata['files']['ktp'];
            }
        }

        if ($request->jenis_layanan == 'Rekomendasi Bantuan') {
            $metadata = [
                'rekomendasi' => $request->rekomendasi,
                'files' => [],
            ];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    $metadata['files'][$key] = $file->store('berkas_rekomendasi', 'public');
                }
            }

            if (isset($metadata['files']['proposal'])) {
                $path = $metadata['files']['proposal'];
            }
        }

        $permohonan = Permohonan::create([
            'user_id' => Auth::id(),
            'jenis_layanan' => $request->jenis_layanan,
            'no_antrean' => $no_antrean,
            'status' => $status,
            'file_path' => $path,
            'metadata' => $metadata,
        ]);

        // Log
        $permohonan->logs()->create([
            'action' => $is_draft ? 'drafted' : 'submitted',
            'actor_id' => Auth::id(),
        ]);

        if ($is_draft) {
            return redirect()->back()->with('success', 'Permohonan berhasil disimpan sebagai draft.');
        }

        return redirect()->back()->with('success', 'Permohonan berhasil dikirim! Nomor Antrean: ' . $no_antrean);
    }
    public function update(Request $request, $id)
    {
        $permohonan = Permohonan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($permohonan->status !== 'pending' && $permohonan->status !== 'ditolak' && $permohonan->status !== 'draft') {
            return redirect()->back()->with('error', 'Permohonan yang sedang diproses tidak dapat diubah.');
        }

        $is_draft = $request->has('is_draft') && $request->is_draft;

        $request->validate([
            'jenis_layanan' => 'required',
            'berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->jenis_layanan == 'Dispen Nikah' && !$is_draft) {
            $request->validate([
                'suami.nama' => 'required|string',
                'suami.nik' => 'required|numeric',
                'istri.nama' => 'required|string',
                'istri.nik' => 'required|numeric',
                'suami.bin' => 'required|string',
                'pernikahan.tanggal' => 'required|date',
                'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
        }

        if ($request->jenis_layanan == 'Izin Keramaian' && !$is_draft) {
            $request->validate([
                'pemohon.nama' => 'required|string',
                'pemohon.nik' => 'required|numeric',
                'keramaian.tanggal' => 'required|string',
                'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
        }

        if ($request->jenis_layanan == 'Rekomendasi Bantuan' && !$is_draft) {
            $request->validate([
                'rekomendasi.jenis_kelompok' => 'required|string',
                'rekomendasi.nama_kelompok' => 'required|string',
                'rekomendasi.alamat' => 'required|string',
                'rekomendasi.perihal' => 'required|string',
                'rekomendasi.nama_desa' => 'required|string',
                'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
        }

        $data = [
            'jenis_layanan' => $request->jenis_layanan,
        ];

        if ($request->jenis_layanan == 'Dispen Nikah') {
            // Merge existing metadata with new request data
            $currentMetadata = $permohonan->metadata ?? [];
            $newFiles = $currentMetadata['files'] ?? [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    // Delete old file if exists
                    if (isset($newFiles[$key]) && Storage::disk('public')->exists($newFiles[$key])) {
                        Storage::disk('public')->delete($newFiles[$key]);
                    }
                    $newFiles[$key] = $file->store('berkas_dispen_nikah', 'public');
                }
            }

            $data['metadata'] = [
                'suami' => $request->suami,
                'istri' => $request->istri,
                'pernikahan' => $request->pernikahan,
                'alasan' => $request->alasan,
                'whatsapp' => $request->whatsapp,
                'files' => $newFiles,
            ];

            // Update main file path if pas_foto changed
            if (isset($newFiles['pas_foto'])) {
                $data['file_path'] = $newFiles['pas_foto'];
            }

        } elseif ($request->jenis_layanan == 'Izin Keramaian') {
            $currentMetadata = $permohonan->metadata ?? [];
            $newFiles = $currentMetadata['files'] ?? [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    if (isset($newFiles[$key]) && Storage::disk('public')->exists($newFiles[$key])) {
                        Storage::disk('public')->delete($newFiles[$key]);
                    }
                    $newFiles[$key] = $file->store('berkas_izin_keramaian', 'public');
                }
            }

            $data['metadata'] = [
                'pemohon' => $request->pemohon,
                'keramaian' => $request->keramaian,
                'files' => $newFiles,
            ];

            if (isset($newFiles['ktp'])) {
                $data['file_path'] = $newFiles['ktp'];
            }

        } elseif ($request->jenis_layanan == 'Rekomendasi Bantuan') {
            $currentMetadata = $permohonan->metadata ?? [];
            $newFiles = $currentMetadata['files'] ?? [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $key => $file) {
                    if (isset($newFiles[$key]) && Storage::disk('public')->exists($newFiles[$key])) {
                        Storage::disk('public')->delete($newFiles[$key]);
                    }
                    $newFiles[$key] = $file->store('berkas_rekomendasi', 'public');
                }
            }

            $data['metadata'] = [
                'rekomendasi' => $request->rekomendasi,
                'files' => $newFiles,
            ];

            if (isset($newFiles['proposal'])) {
                $data['file_path'] = $newFiles['proposal'];
            }

        } else {
            $data['metadata'] = null; // Clear metadata if service type changes
        }

        if ($request->hasFile('berkas')) {
            if (Storage::disk('public')->exists($permohonan->file_path)) {
                Storage::disk('public')->delete($permohonan->file_path);
            }
            $data['file_path'] = $request->file('berkas')->store('berkas_permohonan', 'public');
        }

        // Reset status
        if ($is_draft) {
            $data['status'] = 'draft';
        } else {
            $data['status'] = 'pending';
            // Generate no_antrean if it doesn't have one (if transitioning from draft)
            if (!$permohonan->no_antrean) {
                $logic_number = Permohonan::whereDate('created_at', today())->whereNotNull('no_antrean')->count() + 1;
                $data['no_antrean'] = date('Ymd') . str_pad($logic_number, 3, '0', STR_PAD_LEFT);
            }
        }

        // Remove rejection note
        $data['keterangan'] = null;

        $permohonan->update($data);

        $permohonan->logs()->create([
            'action' => $is_draft ? 'draft_updated' : 'updated',
            'actor_id' => Auth::id(),
        ]);

        $message = $is_draft ? 'Draft berhasil diperbarui.' : 'Permohonan berhasil diperbarui.';
        if (!$is_draft && isset($data['no_antrean'])) {
            $message .= ' Nomor Antrean: ' . $data['no_antrean'];
        }

        return redirect()->back()->with('success', $message);
    }

    public function destroy($id)
    {
        $permohonan = Permohonan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($permohonan->status !== 'pending' && $permohonan->status !== 'ditolak' && $permohonan->status !== 'draft') {
            return redirect()->back()->with('error', 'Permohonan yang sedang diproses tidak dapat dihapus.');
        }

        // Delete main file
        if ($permohonan->file_path && Storage::disk('public')->exists($permohonan->file_path)) {
            Storage::disk('public')->delete($permohonan->file_path);
        }

        // Delete multi-files in metadata
        if ($permohonan->metadata && isset($permohonan->metadata['files'])) {
            foreach ($permohonan->metadata['files'] as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $permohonan->logs()->delete();

        $permohonan->delete();

        return redirect()->back()->with('success', 'Permohonan berhasil dihapus.');
    }

    public function print($id)
    {
        $permohonan = Permohonan::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'selesai')
            ->firstOrFail();

        // Fetch the Camat user (assuming there's only one active Camat or just taking the first one)
        // Ideally we might want to store who approved it, but fetching the current role:camat user is sufficient for now.
        $camat = \App\Models\User::where('role', 'camat')->first();

        if (!$camat) {
            return redirect()->back()->with('error', 'Data Camat tidak ditemukan.');
        }

        if ($permohonan->jenis_layanan === 'Dispen Nikah') {
            return view('preview.surat-dispen', compact('permohonan', 'camat'));
        } elseif ($permohonan->jenis_layanan === 'Izin Keramaian') {
            return view('preview.surat-keramaian', compact('permohonan', 'camat'));
        } elseif ($permohonan->jenis_layanan === 'Rekomendasi Bantuan') {
            return view('preview.surat-rekomendasi', compact('permohonan', 'camat'));
        }

        return redirect()->back()->with('error', 'Cetak belum tersedia untuk layanan ini.');
    }
}
