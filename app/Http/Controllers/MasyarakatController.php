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
            'jenis_layanan' => 'required|in:Dispen Nikah,Izin Keramaian,Rekomendasi Bantuan',
            'berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'jenis_layanan.in' => 'Jenis layanan yang dipilih tidak valid.',
        ]);

        if ($request->jenis_layanan == 'Dispen Nikah' && !$request->has('is_draft')) {
            $request->validate([
                'suami.nama' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
                'suami.nik' => 'required|digits:16',
                'suami.bin' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
                'suami.ttl' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s,0-9]+$/'],
                'suami.agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
                'suami.pekerjaan' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\/\-]+$/'],
                'suami.status' => 'required|in:Belum Kawin,Duda (Cerai Hidup),Duda (Cerai Mati)',
                'suami.alamat' => 'required|string|max:500',

                'istri.nama' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
                'istri.nik' => 'required|digits:16',
                'istri.binti' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
                'istri.ttl' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s,0-9]+$/'],
                'istri.agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
                'istri.pekerjaan' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\/\-]+$/'],
                'istri.status' => 'required|in:Belum Kawin,Janda (Cerai Hidup),Janda (Cerai Mati)',
                'istri.alamat' => 'required|string|max:500',

                'pernikahan.hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
                'pernikahan.tanggal' => 'required|date|after_or_equal:today',
                'pernikahan.waktu' => 'required|date_format:H:i',
                'pernikahan.tempat' => 'required|string|max:200',

                'alasan' => 'required|string|max:1000',
                'whatsapp' => ['required', 'string', 'regex:/^(08|628)[0-9]{8,13}$/'],

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
            ], [
                'suami.nama.regex' => 'Nama suami hanya boleh mengandung huruf, spasi, dan titik.',
                'suami.nama.max' => 'Nama suami maksimal 100 karakter.',
                'suami.nik.digits' => 'NIK suami harus tepat 16 digit angka.',
                'suami.bin.regex' => 'Nama ayah (bin) hanya boleh mengandung huruf, spasi, dan titik.',
                'suami.ttl.regex' => 'Tempat, tanggal lahir hanya boleh mengandung huruf, angka, spasi, dan koma.',
                'suami.agama.in' => 'Agama suami yang dipilih tidak valid.',
                'suami.pekerjaan.regex' => 'Pekerjaan suami hanya boleh mengandung huruf, spasi, garis miring, dan strip.',
                'suami.status.in' => 'Status suami yang dipilih tidak valid.',
                'suami.alamat.max' => 'Alamat suami maksimal 500 karakter.',
                'istri.nama.regex' => 'Nama istri hanya boleh mengandung huruf, spasi, dan titik.',
                'istri.nama.max' => 'Nama istri maksimal 100 karakter.',
                'istri.nik.digits' => 'NIK istri harus tepat 16 digit angka.',
                'istri.binti.regex' => 'Nama ayah (binti) hanya boleh mengandung huruf, spasi, dan titik.',
                'istri.ttl.regex' => 'Tempat, tanggal lahir hanya boleh mengandung huruf, angka, spasi, dan koma.',
                'istri.agama.in' => 'Agama istri yang dipilih tidak valid.',
                'istri.pekerjaan.regex' => 'Pekerjaan istri hanya boleh mengandung huruf, spasi, garis miring, dan strip.',
                'istri.status.in' => 'Status istri yang dipilih tidak valid.',
                'istri.alamat.max' => 'Alamat istri maksimal 500 karakter.',
                'pernikahan.hari.in' => 'Hari yang dipilih tidak valid.',
                'pernikahan.tanggal.after_or_equal' => 'Tanggal pernikahan harus hari ini atau setelahnya.',
                'pernikahan.waktu.date_format' => 'Format waktu harus HH:MM (contoh: 08:00).',
                'pernikahan.tempat.max' => 'Tempat akad maksimal 200 karakter.',
                'alasan.max' => 'Alasan maksimal 1000 karakter.',
                'whatsapp.regex' => 'Nomor WhatsApp tidak valid (contoh: 08xxxxxxxxxx).',
            ]);
        }

        if ($request->jenis_layanan == 'Izin Keramaian' && !$request->has('is_draft')) {
            $request->validate([
                'pemohon.nama' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
                'pemohon.ttl' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s,0-9]+$/'],
                'pemohon.gender' => 'required|in:Laki-laki,Perempuan',
                'pemohon.nik' => 'required|digits:16',
                'pemohon.pekerjaan' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\/\-]+$/'],
                'pemohon.alamat' => 'required|string|max:500',
                'keramaian.tanggal' => 'required|string|max:100',
                'keramaian.acara' => ['required', 'string', 'max:200', 'regex:/^[a-zA-Z\s\/\-]+$/'],
                'keramaian.lokasi' => 'required|string|max:500',
                'keramaian.hiburan' => ['required', 'string', 'max:200', 'regex:/^[a-zA-Z\s\/\-]+$/'],
                'files.ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'files.proposal_acara' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ], [
                'pemohon.nama.regex' => 'Nama pemohon hanya boleh mengandung huruf, spasi, dan titik.',
                'pemohon.nama.max' => 'Nama pemohon maksimal 100 karakter.',
                'pemohon.ttl.regex' => 'Tempat, tanggal lahir hanya boleh mengandung huruf, angka, spasi, dan koma.',
                'pemohon.gender.in' => 'Jenis kelamin yang dipilih tidak valid.',
                'pemohon.nik.digits' => 'NIK pemohon harus tepat 16 digit angka.',
                'pemohon.pekerjaan.regex' => 'Pekerjaan hanya boleh mengandung huruf, spasi, garis miring, dan strip.',
                'pemohon.alamat.max' => 'Alamat maksimal 500 karakter.',
                'keramaian.acara.regex' => 'Nama acara hanya boleh mengandung huruf, spasi, garis miring, dan strip.',
                'keramaian.lokasi.max' => 'Lokasi maksimal 500 karakter.',
                'keramaian.hiburan.regex' => 'Hiburan hanya boleh mengandung huruf, spasi, garis miring, dan strip.',
            ]);
        }

        if ($request->jenis_layanan == 'Rekomendasi Bantuan' && !$request->has('is_draft')) {
            $request->validate([
                'rekomendasi.jenis_kelompok' => ['required', 'string', 'max:200', 'regex:/^[a-zA-Z\s\/\-]+$/'],
                'rekomendasi.nama_kelompok' => ['required', 'string', 'max:200', 'regex:/^[a-zA-Z0-9\s\.\-]+$/'],
                'rekomendasi.alamat' => 'required|string|max:500',
                'rekomendasi.perihal' => 'required|string|max:500',
                'rekomendasi.nama_desa' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
                'files.proposal' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ], [
                'rekomendasi.jenis_kelompok.regex' => 'Jenis kelompok hanya boleh mengandung huruf, spasi, garis miring, dan strip.',
                'rekomendasi.jenis_kelompok.max' => 'Jenis kelompok maksimal 200 karakter.',
                'rekomendasi.nama_kelompok.regex' => 'Nama kelompok hanya boleh mengandung huruf, angka, spasi, titik, dan strip.',
                'rekomendasi.nama_kelompok.max' => 'Nama kelompok maksimal 200 karakter.',
                'rekomendasi.alamat.max' => 'Alamat maksimal 500 karakter.',
                'rekomendasi.perihal.max' => 'Perihal maksimal 500 karakter.',
                'rekomendasi.nama_desa.regex' => 'Nama desa hanya boleh mengandung huruf dan spasi.',
                'rekomendasi.nama_desa.max' => 'Nama desa maksimal 100 karakter.',
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
            'jenis_layanan' => 'required|in:Dispen Nikah,Izin Keramaian,Rekomendasi Bantuan',
            'berkas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'jenis_layanan.in' => 'Jenis layanan yang dipilih tidak valid.',
        ]);

        if ($request->jenis_layanan == 'Dispen Nikah' && !$is_draft) {
            $request->validate([
                'suami.nama' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
                'suami.nik' => 'required|digits:16',
                'istri.nama' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
                'istri.nik' => 'required|digits:16',
                'suami.bin' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
                'pernikahan.tanggal' => 'required|date|after_or_equal:today',
                'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ], [
                'suami.nama.regex' => 'Nama suami hanya boleh mengandung huruf, spasi, dan titik.',
                'suami.nik.digits' => 'NIK suami harus tepat 16 digit angka.',
                'istri.nama.regex' => 'Nama istri hanya boleh mengandung huruf, spasi, dan titik.',
                'istri.nik.digits' => 'NIK istri harus tepat 16 digit angka.',
                'suami.bin.regex' => 'Nama ayah (bin) hanya boleh mengandung huruf, spasi, dan titik.',
                'pernikahan.tanggal.after_or_equal' => 'Tanggal pernikahan harus hari ini atau setelahnya.',
            ]);
        }

        if ($request->jenis_layanan == 'Izin Keramaian' && !$is_draft) {
            $request->validate([
                'pemohon.nama' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\.\',]+$/'],
                'pemohon.nik' => 'required|digits:16',
                'keramaian.tanggal' => 'required|string|max:100',
                'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ], [
                'pemohon.nama.regex' => 'Nama pemohon hanya boleh mengandung huruf, spasi, dan titik.',
                'pemohon.nik.digits' => 'NIK pemohon harus tepat 16 digit angka.',
            ]);
        }

        if ($request->jenis_layanan == 'Rekomendasi Bantuan' && !$is_draft) {
            $request->validate([
                'rekomendasi.jenis_kelompok' => ['required', 'string', 'max:200', 'regex:/^[a-zA-Z\s\/\-]+$/'],
                'rekomendasi.nama_kelompok' => ['required', 'string', 'max:200', 'regex:/^[a-zA-Z0-9\s\.\-]+$/'],
                'rekomendasi.alamat' => 'required|string|max:500',
                'rekomendasi.perihal' => 'required|string|max:500',
                'rekomendasi.nama_desa' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
                'files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ], [
                'rekomendasi.jenis_kelompok.regex' => 'Jenis kelompok hanya boleh mengandung huruf, spasi, garis miring, dan strip.',
                'rekomendasi.nama_kelompok.regex' => 'Nama kelompok hanya boleh mengandung huruf, angka, spasi, titik, dan strip.',
                'rekomendasi.nama_desa.regex' => 'Nama desa hanya boleh mengandung huruf dan spasi.',
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
            // FCFS: Generate new no_antrean if transitioning from draft (no queue number yet)
            // OR if resubmitting after rejection (must go to back of queue)
            if (!$permohonan->no_antrean || $permohonan->status === 'ditolak') {
                $logic_number = Permohonan::whereDate('created_at', today())->whereNotNull('no_antrean')->count() + 1;
                $data['no_antrean'] = date('Ymd') . str_pad($logic_number, 3, '0', STR_PAD_LEFT);
                // Reset timestamp so the displayed time reflects resubmission, not original submission
                $data['created_at'] = now();
            }
        }

        // Remove rejection note
        $data['keterangan'] = null;

        $permohonan->update($data);

        // Determine log action: resubmitted (from ditolak), draft_updated, or updated
        $logAction = $is_draft ? 'draft_updated' : ($permohonan->getOriginal('status') === 'ditolak' ? 'resubmitted' : 'updated');
        $permohonan->logs()->create([
            'action' => $logAction,
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
