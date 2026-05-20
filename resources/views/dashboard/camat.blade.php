<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Camat') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        activeTab: 'dashboard',
        showModal: false, 
        selectedId: null, 
        actionType: '', 
        keterangan: '',
        showPreviewModal: false,
        previewUrl: ''
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Sidebar -->
                <div class="w-full md:w-64 flex-shrink-0">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 sticky top-24">
                        <div class="p-4 space-y-2">
                            <button @click="activeTab = 'dashboard'" 
                                :class="activeTab === 'dashboard' ? 'bg-bedas-50 text-bedas-600' : 'text-slate-600 hover:bg-slate-50'"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-medium text-left">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                Dashboard
                            </button>
                            <button @click="activeTab = 'approval'" 
                                :class="activeTab === 'approval' ? 'bg-bedas-50 text-bedas-600' : 'text-slate-600 hover:bg-slate-50'"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-medium text-left">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Persetujuan Dokumen
                                @if($pendingCount > 0)
                                    <span class="ml-auto bg-orange-100 text-orange-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                                @endif
                            </button>
                            <button @click="activeTab = 'history'" 
                                :class="activeTab === 'history' ? 'bg-bedas-50 text-bedas-600' : 'text-slate-600 hover:bg-slate-50'"
                                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-medium text-left">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Riwayat Disetujui
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">
                    @if(session('success'))
                        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Dashboard Tab (Stats) -->
                    <div x-show="activeTab === 'dashboard'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 mb-8">
                            <div class="p-8">
                                <h3 class="text-xl font-bold text-slate-800 mb-6">Ringkasan Statistik</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                                    <div class="bg-bedas-50 p-6 rounded-2xl border border-bedas-100">
                                        <h4 class="text-bedas-600 font-medium text-sm">Total Permohonan</h4>
                                        <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalPermohonan }}</p>
                                    </div>
                                    <div class="bg-orange-50 p-6 rounded-2xl border border-orange-100">
                                        <h4 class="text-orange-600 font-medium text-sm">Menunggu TTD</h4>
                                        <p class="text-3xl font-bold text-slate-800 mt-2">{{ $pendingCount }}</p>
                                    </div>
                                    <div class="bg-emerald-50 p-6 rounded-2xl border border-emerald-100">
                                        <h4 class="text-emerald-600 font-medium text-sm">Disetujui</h4>
                                        <p class="text-3xl font-bold text-slate-800 mt-2">{{ $approvedCount }}</p>
                                    </div>
                                    <div class="bg-red-50 p-6 rounded-2xl border border-red-100">
                                        <h4 class="text-red-600 font-medium text-sm">Ditolak</h4>
                                        <p class="text-3xl font-bold text-slate-800 mt-2">{{ $rejectedCount }}</p>
                                    </div>
                                </div>
            
                                <div class="h-80 w-full">
                                    <canvas id="permohonanChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Tab (Pending) -->
                    <div x-show="activeTab === 'approval'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                            <div class="p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-800">Persetujuan Dokumen</h3>
                                        <p class="text-slate-500 text-sm">Validasi akhir dan penandatanganan digital.</p>
                                    </div>
                                </div>
            
                                @if($permohonans->isEmpty())
                                    <div class="rounded-xl bg-slate-50 border border-slate-100 p-12 text-center">
                                        <p class="text-slate-500 italic">Tidak ada dokumen yang menunggu persetujuan saat ini.</p>
                                    </div>
                                @else
                                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                                        <table class="min-w-full leading-normal">
                                            <thead>
                                                <tr>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">No Antrean</th>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemohon</th>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Layanan</th>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Masuk</th>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Draft Surat</th>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-slate-100">
                                                @foreach($permohonans as $p)
                                                    <tr class="hover:bg-slate-50 transition-colors">
                                                        <td class="px-6 py-4 whitespace-no-wrap text-sm font-mono font-medium text-slate-700">#{{ $p->no_antrean }}</td>
                                                        <td class="px-6 py-4 whitespace-no-wrap">
                                                            <div class="flex items-center">
                                                                <div class="h-8 w-8 rounded-full bg-bedas-100 flex items-center justify-center text-bedas-700 font-bold text-xs mr-3">
                                                                    {{ substr($p->user->name, 0, 1) }}
                                                                </div>
                                                                <span class="text-sm font-medium text-slate-800">{{ $p->user->name }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-no-wrap text-sm text-slate-600">{{ $p->jenis_layanan }}</td>
                                                        <td class="px-6 py-4 whitespace-no-wrap text-sm text-slate-500">{{ $p->created_at->format('d M Y') }}</td>
                                                        <td class="px-6 py-4 whitespace-no-wrap text-sm">
                                                            @if(in_array($p->jenis_layanan, ['Dispen Nikah', 'Izin Keramaian', 'Rekomendasi Bantuan']))
                                                                <button @click="previewUrl = '{{ route('camat.preview', $p->id) }}'; showPreviewModal = true;" type="button" class="inline-flex items-center gap-1 text-bedas-600 hover:text-bedas-800 font-medium">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                                    Lihat Draft
                                                                </button>
                                                            @else
                                                                <span class="text-slate-400 text-xs italic">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-no-wrap text-sm flex gap-3">
                                                            <button @click="showModal = true; selectedId = {{ $p->id }}; actionType = 'reject'"
                                                                class="text-red-600 hover:text-red-800 font-medium text-sm transition-colors">
                                                                Tolak
                                                            </button>
                                                            <button @click="showModal = true; selectedId = {{ $p->id }}; actionType = 'approve'"
                                                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded-lg shadow-md shadow-blue-200 transition-all transform hover:-translate-y-0.5 text-xs">
                                                                Setujui & TTD
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- History Tab -->
                    <div x-show="activeTab === 'history'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                            <div class="p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-800">Riwayat Disetujui / Ditolak</h3>
                                        <p class="text-slate-500 text-sm">Daftar permohonan yang telah diproses.</p>
                                    </div>
                                </div>
            
                                @if($history->isEmpty())
                                    <div class="rounded-xl bg-slate-50 border border-slate-100 p-12 text-center">
                                        <p class="text-slate-500 italic">Belum ada riwayat permohonan.</p>
                                    </div>
                                @else
                                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                                        <table class="min-w-full leading-normal">
                                            <thead>
                                                <tr>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">No Antrean</th>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemohon</th>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Layanan</th>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Proses</th>
                                                    <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-slate-100">
                                                @foreach($history as $h)
                                                    <tr class="hover:bg-slate-50 transition-colors">
                                                        <td class="px-6 py-4 whitespace-no-wrap text-sm font-mono font-medium text-slate-700">#{{ $h->no_antrean }}</td>
                                                        <td class="px-6 py-4 whitespace-no-wrap">
                                                            <div class="flex items-center">
                                                                <div class="h-8 w-8 rounded-full bg-bedas-100 flex items-center justify-center text-bedas-700 font-bold text-xs mr-3">
                                                                    {{ substr($h->user->name, 0, 1) }}
                                                                </div>
                                                                <span class="text-sm font-medium text-slate-800">{{ $h->user->name }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-no-wrap text-sm text-slate-600">{{ $h->jenis_layanan }}</td>
                                                        <td class="px-6 py-4 whitespace-no-wrap text-sm text-slate-500">{{ $h->updated_at->format('d M Y') }}</td>
                                                        <td class="px-6 py-4 whitespace-no-wrap text-sm">
                                                            @if($h->status == 'ditandatangani')
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                                    Ditandatangani
                                                                </span>
                                                            @elseif($h->status == 'selesai')
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                                    Selesai
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                    Ditolak
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Preview Modal -->
        <div x-show="showPreviewModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showPreviewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showPreviewModal = false">
                    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>
                </div>

                <div x-show="showPreviewModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full h-[85vh] flex flex-col">
                    
                    <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex justify-between items-center shrink-0">
                        <h3 class="text-lg font-bold text-slate-800">Preview Draft Surat</h3>
                        <button @click="showPreviewModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 w-full bg-slate-100 p-0 overflow-hidden relative">
                        <template x-if="showPreviewModal">
                            <iframe :src="previewUrl" class="w-full h-full border-0 absolute top-0 left-0"></iframe>
                        </template>
                    </div>
                    
                    <div class="bg-gray-50 border-t border-slate-200 px-6 py-4 flex justify-end shrink-0">
                         <button type="button" @click="showPreviewModal = false" class="inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none transition-colors">
                            Tutup Preview
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Modal -->
        <div x-show="showModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <form x-bind:action="'/camat/approve/' + selectedId" method="POST">
                        @csrf
                        <input type="hidden" name="action" x-model="actionType">

                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full"
                                :class="actionType === 'reject' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'">
                                <svg x-show="actionType === 'reject'" class="w-6 h-6" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <svg x-show="actionType === 'approve'" class="w-6 h-6" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>

                            <div class="text-center">
                                <h3 class="text-xl leading-6 font-bold text-slate-900 mb-2"
                                    x-text="actionType === 'reject' ? 'Tolak Permohonan' : 'Konfirmasi Persetujuan'">
                                </h3>

                                <div x-show="actionType === 'reject'" class="mt-4 text-left">
                                    <p class="text-sm text-slate-500 mb-2">Mohon berikan alasan penolakan:</p>
                                    <textarea name="keterangan" rows="3"
                                        class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                                        placeholder="Alasan..."></textarea>
                                </div>
                                <p class="mt-2 text-sm text-slate-500" x-show="actionType === 'approve'">
                                    Dengan menyetujui, dokumen digital akan diterbitkan dan ditandatangani secara
                                    elektronik. Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent px-4 py-2 text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors sm:w-auto"
                                :class="actionType === 'reject' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'">
                                <span x-text="actionType === 'reject' ? 'Tolak' : 'Setujui & Terbitkan'"></span>
                            </button>
                            <button type="button" @click="showModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('permohonanChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Jumlah Permohonan',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.5)', // Blue
                            'rgba(16, 185, 129, 0.5)', // Green
                            'rgba(139, 92, 246, 0.5)'  // Violet
                        ],
                        borderColor: [
                            'rgb(59, 130, 246)',
                            'rgb(16, 185, 129)',
                            'rgb(139, 92, 246)'
                        ],
                        borderWidth: 1,
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        title: { display: true, text: 'Statistik Permohonan per Layanan' }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>

</x-app-layout>