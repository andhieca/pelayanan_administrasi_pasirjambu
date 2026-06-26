<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CamatController extends Controller
{
    public function index()
    {
        $permohonans = Permohonan::where('status', 'menunggu_camat')
            ->orderBy('no_antrean', 'asc')
            ->get();

        $history = Permohonan::whereIn('status', ['ditandatangani', 'selesai', 'ditolak'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Statistics - Exclude Drafts
        $totalPermohonan = Permohonan::where('status', '!=', 'draft')->count();
        $pendingCount = Permohonan::where('status', 'menunggu_camat')->count();
        $approvedCount = Permohonan::whereIn('status', ['ditandatangani', 'selesai'])->count();
        $rejectedCount = Permohonan::where('status', 'ditolak')->count();

        // Data for Charts (Grouped by Service Type) - Exclude Drafts
        $statsByLayanan = Permohonan::where('status', '!=', 'draft')
            ->select('jenis_layanan', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('jenis_layanan')
            ->pluck('total', 'jenis_layanan')
            ->toArray();

        // Ensure all keys exist for the chart even if 0
        $layananTypes = ['Dispen Nikah', 'Rekomendasi Bantuan', 'Izin Keramaian'];
        $chartData = [];
        foreach ($layananTypes as $type) {
            $chartData[] = $statsByLayanan[$type] ?? 0;
        }

        $chartLabels = $layananTypes;

        return view('dashboard.camat', compact(
            'permohonans',
            'totalPermohonan',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'chartData',
            'chartLabels',
            'history'
        ));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'keterangan' => 'required_if:action,reject',
        ]);

        $permohonan = Permohonan::findOrFail($id);

        if ($request->action === 'reject') {
            $permohonan->update([
                'status' => 'ditolak',
                'keterangan' => 'Ditolak oleh Camat: ' . $request->keterangan,
            ]);
            $permohonan->logs()->create([
                'action' => 'rejected_camat',
                'actor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Permohonan ditolak.');
        } else {
            $permohonan->update([
                'status' => 'ditandatangani',
                'verification_token' => Str::uuid()->toString(),
            ]);
            $permohonan->logs()->create([
                'action' => 'signed_camat',
                'actor_id' => Auth::id(),
            ]);
            return redirect()->back()->with('success', 'Dokumen telah ditandatangani. Menunggu penomoran oleh petugas.');
        }
    }
    public function preview($id)
    {
        $permohonan = Permohonan::findOrFail($id);

        if ($permohonan->jenis_layanan === 'Dispen Nikah') {
            $camat = Auth::user();
            return view('preview.surat-dispen', compact('permohonan', 'camat'));
        } elseif ($permohonan->jenis_layanan === 'Izin Keramaian') {
            $camat = Auth::user();
            return view('preview.surat-keramaian', compact('permohonan', 'camat'));
        } elseif ($permohonan->jenis_layanan === 'Rekomendasi Bantuan') {
            $camat = Auth::user();
            return view('preview.surat-rekomendasi', compact('permohonan', 'camat'));
        }

        return redirect()->back()->with('error', 'Preview belum tersedia untuk layanan ini.');
    }

    public function printReport(Request $request)
    {
        // Fetch historical data (signed, finished, rejected)
        $history = Permohonan::whereIn('status', ['ditandatangani', 'selesai', 'ditolak'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Calculate statistics for the report
        $total = $history->count();
        $disetujui = $history->whereIn('status', ['ditandatangani', 'selesai'])->count();
        $ditolak = $history->where('status', 'ditolak')->count();

        $camat = Auth::user();

        return view('dashboard.camat-report', compact('history', 'total', 'disetujui', 'ditolak', 'camat'));
    }
}
