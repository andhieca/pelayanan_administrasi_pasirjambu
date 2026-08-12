<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Dashboard Petugas') }}
            </h2>
            <div class="relative group cursor-pointer">
                <div class="p-2 bg-white rounded-full hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all shadow-sm group-hover:shadow-md">
                    <svg class="w-6 h-6 text-slate-600 group-hover:text-bedas-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if($permohonans->count() > 0)
                        <span class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white animate-pulse">
                            {{ $permohonans->count() }}
                        </span>
                    @endif
                </div>
                <!-- Tooltip -->
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-slate-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 transform origin-top-right">
                    <p class="px-4 py-2 text-xs text-slate-500 font-semibold border-b border-slate-50">Notifikasi</p>
                    <div class="px-4 py-2">
                        @if($permohonans->count() > 0)
                            <p class="text-sm text-slate-700">Ada <span class="font-bold text-bedas-600">{{ $permohonans->count() }}</span> permohonan baru menunggu validasi.</p>
                        @else
                            <p class="text-xs text-slate-400">Tidak ada permohonan baru.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        showDetailModal: false, 
        selectedPermohonan: null,
        showNomorModal: false,
        nomorSuratId: null,
        
        // File Preview State
        showFileModal: false,
        activeFileSrc: null,
        activeFileType: 'image',
        activeFileKey: null,
        validatedFiles: {}, // Format: { permohonanId: { fileKey: true } }
        
        // Verifikasi State
        invalidItems: [], // Array of string descriptions for invalid items
        showKeteranganInput: false,

        init() {
            this.$watch('invalidItems', value => {
                const detailInput = document.getElementById('detail_invalid_items_json');
                if (detailInput) detailInput.value = JSON.stringify(value);
                const simpleInput = document.getElementById('simple_invalid_items_json');
                if (simpleInput) simpleInput.value = JSON.stringify(value);
            });
        },

        openDetail(p) {
            this.selectedPermohonan = p;
            this.invalidItems = []; // reset
            this.showKeteranganInput = false;
            this.showDetailModal = true;
        },

        openFilePreview(path, key) {
            this.activeFileSrc = '/berkas/' + path;
            this.activeFileKey = key;
            const extension = path.split('.').pop().toLowerCase();
            this.activeFileType = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension) ? 'image' : 'pdf';
            this.showFileModal = true;
        },

        formatFileKey(key) {
            if (!key) return '';
            return 'Berkas ' + key.replace('_', ' ').toUpperCase();
        },

        isFileRejected(key) {
            const fileKey = key || this.activeFileKey;
            return this.invalidItems.includes(this.formatFileKey(fileKey));
        },

        toggleFileReject() {
            if (!this.selectedPermohonan) return;
            const formattedKey = this.formatFileKey(this.activeFileKey);
            const index = this.invalidItems.indexOf(formattedKey);
            
            if (index === -1) {
                this.invalidItems.push(formattedKey);
            } else {
                this.invalidItems.splice(index, 1);
            }
            this.showFileModal = false;
        },

        isFileValidated(key) {
             if (!this.selectedPermohonan) return false;
             const pId = this.selectedPermohonan.id;
             return this.validatedFiles[pId] && this.validatedFiles[pId][key];
        }
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Antrean Masuk</h3>
                            <p class="text-slate-500 text-sm">Proses permohonan berdasarkan urutan.</p>
                        </div>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">Pending: {{ $permohonans->count() }}</span>
                    </div>

                    @if($permohonans->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">📭</span>
                            </div>
                            <p class="text-slate-500">Tidak ada antrean permohonan baru.</p>
                        </div>
                    @else
                        <div class="relative space-y-6 before:content-[''] before:absolute before:left-8 before:top-4 before:bottom-4 before:w-0.5 before:bg-slate-100">
                            @foreach($permohonans as $index => $p)
                                <div class="relative pl-20 group">
                                    <!-- Timeline Dot -->
                                    <div class="absolute left-6 top-6 w-4 h-4 rounded-full border-4 border-white shadow-sm z-10 
                                        {{ $index === 0 ? 'bg-bedas-500 ring-4 ring-bedas-50' : 'bg-slate-200' }}">
                                    </div>

                                    <div class="p-6 rounded-2xl border transition-all duration-300
                                        {{ $index === 0 ? 'bg-white border-bedas-100 shadow-lg shadow-bedas-100/50 scale-[1.02]' : 'bg-slate-50 border-slate-100 opacity-75 grayscale-[0.5] hover:grayscale-0' }}">

                                        <div class="flex flex-col sm:flex-row sm:justify-between items-start mb-4 gap-2 sm:gap-0">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-1">
                                                    <span class="font-mono text-sm font-bold text-slate-400">#{{ $p->no_antrean }}</span>
                                                    @if($index === 0)
                                                        <span class="px-2 py-0.5 bg-bedas-100 text-bedas-700 text-[10px] font-bold uppercase tracking-wide rounded-full whitespace-nowrap">Giliran Proses</span>
                                                    @else
                                                        <span class="px-2 py-0.5 bg-slate-200 text-slate-500 text-[10px] font-bold uppercase tracking-wide rounded-full whitespace-nowrap">Menunggu</span>
                                                    @endif
                                                </div>
                                                <h4 class="text-lg font-bold text-slate-800">{{ $p->jenis_layanan }}</h4>
                                                <p class="text-slate-500 text-sm">Pemohon: <span class="font-medium text-slate-700">{{ $p->user->name }}</span></p>
                                            </div>
                                            <span class="text-xs text-slate-400 font-mono">{{ $p->created_at->format('H:i') }}</span>
                                        </div>

                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-t border-slate-100 pt-4 mt-4 gap-4 sm:gap-0">
                                            @if($index === 0)
                                                <button @click="openDetail({{ $p }})" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold hover:from-emerald-600 hover:to-green-700 shadow-lg shadow-emerald-200/50 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
                                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Verifikasi Berkas
                                                </button>
                                            @endif

                                            <div class="flex w-full sm:w-auto gap-2 sm:gap-3">
                                                @if($index !== 0)
                                                    <span class="flex-1 sm:flex-none justify-center px-4 py-2 text-slate-400 text-sm font-medium cursor-not-allowed flex items-center gap-1 bg-slate-50 sm:bg-transparent rounded-lg sm:rounded-none">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                        Terkunci
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Signed Documents - Waiting for Numbering -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 mt-8">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Sudah Ditandatangani Camat</h3>
                            <p class="text-slate-500 text-sm">Berkas yang sudah ditandatangani, perlu penomoran surat.</p>
                        </div>
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-xs font-medium">Menunggu Nomor: {{ $ditandatangani->count() }}</span>
                    </div>

                    @if($ditandatangani->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">📋</span>
                            </div>
                            <p class="text-slate-500">Tidak ada berkas yang menunggu penomoran.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-slate-100">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">No Antrean</th>
                                        <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemohon</th>
                                        <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Layanan</th>
                                        <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">TTD Camat</th>
                                        <th class="px-6 py-4 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @foreach($ditandatangani as $d)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-no-wrap text-sm font-mono font-medium text-slate-700">#{{ $d->no_antrean }}</td>
                                            <td class="px-6 py-4 whitespace-no-wrap">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs mr-3">
                                                        {{ substr($d->user->name, 0, 1) }}
                                                    </div>
                                                    <span class="text-sm font-medium text-slate-800">{{ $d->user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-sm text-slate-600">{{ $d->jenis_layanan }}</td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Sudah TTD
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-sm">
                                                <button @click="showNomorModal = true; nomorSuratId = {{ $d->id }}"
                                                    class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg shadow-md shadow-indigo-200 hover:bg-indigo-700 transition-all transform hover:-translate-y-0.5 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                                    Beri Nomor Surat
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

        <!-- Validation Modal -->
        <div x-show="showModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <form x-bind:action="'/petugas/validate/' + selectedId" method="POST">
                        @csrf
                        <input type="hidden" name="action" x-model="actionType">
                        <input type="hidden" id="simple_invalid_items_json" name="invalid_items_json" :value="JSON.stringify(invalidItems)">
                        
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full" 
                                :class="actionType === 'reject' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'">
                                <svg x-show="actionType === 'reject'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                <svg x-show="actionType === 'approve'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            
                            <div class="text-center">
                                <h3 class="text-xl leading-6 font-bold text-slate-900 mb-2" x-text="actionType === 'reject' ? 'Tolak Permohonan' : 'Validasi & Ajukan ke Camat'"></h3>
                                <p class="text-sm text-slate-500 mb-6" x-text="actionType === 'reject' ? 'Mohon berikan alasan penolakan agar pemohon dapat memperbaiki.' : 'Berkas akan diajukan ke Camat untuk ditandatangani. Penomoran surat dilakukan setelah Camat menandatangani.'"></p>
                                
                                 <div x-show="actionType === 'reject'" class="mt-4 text-left">
                                     <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wide mb-2">Alasan Penolakan</label>
                                     <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm" placeholder="Contoh: KTP buram, KK tidak sesuai..."></textarea>
                                 </div>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" @click="const el = document.getElementById('simple_invalid_items_json'); if(el) el.value = JSON.stringify(invalidItems)"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent px-4 py-2 text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto transition-colors"
                                :class="actionType === 'reject' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500'">
                                <span x-text="actionType === 'reject' ? 'Tolak Permohonan' : 'Validasi & Ajukan ke Camat'"></span>
                            </button>
                            <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Detail Modal -->
        <div x-show="showDetailModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDetailModal" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showDetailModal = false">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showDetailModal" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold text-slate-900">Detail Permohonan</h3>
                            <button @click="showDetailModal = false" class="text-slate-400 hover:text-slate-500">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <template x-if="selectedPermohonan">
                            <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide">Pemohon</label>
                                    <p class="text-lg font-bold text-slate-800" x-text="selectedPermohonan.user?.name || '...'"></p>
                                    <p class="text-sm font-mono text-slate-500" x-text="'Antrean #' + selectedPermohonan.no_antrean"></p>
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide">Layanan</label>
                                    <p class="text-slate-800" x-text="selectedPermohonan.jenis_layanan"></p>
                                </div>

                                <!-- Metadata Display for Dispen Nikah -->
                                <template x-if="selectedPermohonan.jenis_layanan === 'Dispen Nikah' && selectedPermohonan.metadata">
                                    <div class="space-y-4">
                                        <!-- Data Pasangan -->
                                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                            <h4 class="font-bold text-slate-800 mb-3 text-sm flex items-center gap-2">
                                                <span>❤️</span> Data Pasangan
                                            </h4>
                                            <div class="grid grid-cols-1 gap-4 text-sm">
                                                <div class="pb-3 border-b border-slate-200">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <p class="text-xs text-slate-500 font-semibold">CALON SUAMI</p>
                                                        <label class="flex items-center gap-1 cursor-pointer">
                                                            <input type="checkbox" value="Data Calon Suami" x-model="invalidItems" class="rounded text-red-500 focus:ring-red-500 border-slate-300 w-3.5 h-3.5">
                                                            <span class="text-xs text-red-500 font-medium">Tidak Sesuai</span>
                                                        </label>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-x-2 gap-y-1">
                                                        <span class="text-slate-400">Nama:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.suami?.nama"></span>
                                                        <span class="text-slate-400">NIK:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.suami?.nik"></span>
                                                        <span class="text-slate-400">TTL:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.suami?.ttl"></span>
                                                        <span class="text-slate-400">Agama:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.suami?.agama"></span>
                                                        <span class="text-slate-400">Pekerjaan:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.suami?.pekerjaan"></span>
                                                        <span class="text-slate-400">Alamat:</span> <span class="text-slate-700 col-span-2" x-text="selectedPermohonan.metadata.suami?.alamat"></span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex justify-between items-center mb-2">
                                                        <p class="text-xs text-slate-500 font-semibold">CALON ISTRI</p>
                                                        <label class="flex items-center gap-1 cursor-pointer">
                                                            <input type="checkbox" value="Data Calon Istri" x-model="invalidItems" class="rounded text-red-500 focus:ring-red-500 border-slate-300 w-3.5 h-3.5">
                                                            <span class="text-xs text-red-500 font-medium">Tidak Sesuai</span>
                                                        </label>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-x-2 gap-y-1">
                                                        <span class="text-slate-400">Nama:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.istri?.nama"></span>
                                                        <span class="text-slate-400">NIK:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.istri?.nik"></span>
                                                        <span class="text-slate-400">TTL:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.istri?.ttl"></span>
                                                        <span class="text-slate-400">Agama:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.istri?.agama"></span>
                                                        <span class="text-slate-400">Pekerjaan:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.istri?.pekerjaan"></span>
                                                        <span class="text-slate-400">Alamat:</span> <span class="text-slate-700 col-span-2" x-text="selectedPermohonan.metadata.istri?.alamat"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Rencana Pernikahan -->
                                        <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                                    <span>📅</span> Rencana Pernikahan
                                                </h4>
                                                <label class="flex items-center gap-1 cursor-pointer">
                                                    <input type="checkbox" value="Rencana Pernikahan" x-model="invalidItems" class="rounded text-red-500 focus:ring-red-500 border-purple-300 w-3.5 h-3.5">
                                                    <span class="text-xs text-red-500 font-medium">Tidak Sesuai</span>
                                                </label>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="block text-xs text-slate-500">Hari & Tanggal</span>
                                                    <p class="font-medium text-slate-800" x-text="(selectedPermohonan.metadata.pernikahan?.hari || '-') + ', ' + (selectedPermohonan.metadata.pernikahan?.tanggal || '-')"></p>
                                                </div>
                                                <div>
                                                    <span class="block text-xs text-slate-500">Waktu & Tempat</span>
                                                    <p class="font-medium text-slate-800" x-text="(selectedPermohonan.metadata.pernikahan?.waktu || '-') + ' di ' + (selectedPermohonan.metadata.pernikahan?.tempat || '-')"></p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Lainnya -->
                                         <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                             <h4 class="text-xs font-bold text-slate-500 uppercase mb-1">Alasan Pengajuan</h4>
                                             <p class="text-sm text-slate-700" x-text="selectedPermohonan.metadata.alasan || '-'"></p>
                                         </div>
                                         <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                             <h4 class="text-xs font-bold text-slate-500 uppercase mb-1">WhatsApp</h4>
                                             <p class="text-sm text-slate-700 font-mono" x-text="selectedPermohonan.metadata.whatsapp || '-'"></p>
                                         </div>

                                        <!-- File List (Dispen Nikah) -->
                                        <div class="mt-4">
                                            <h4 class="font-bold text-slate-800 mb-3 text-sm">Berkas Lampiran</h4>
                                            <template x-if="selectedPermohonan.metadata.files">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    <template x-for="(path, key) in selectedPermohonan.metadata.files" :key="key">
                                                        <button type="button" @click="openFilePreview(path, key)" class="flex items-center p-2 bg-white border rounded-lg hover:shadow-sm transition-all group w-full text-left relative overflow-hidden"
                                                            :class="isFileRejected(key) ? 'border-red-500 bg-red-50' : 'border-slate-200 hover:border-bedas-300'">
                                                            
                                                            <!-- Rejected Badge -->
                                                            <div x-show="isFileRejected(key)" class="absolute top-0 right-0 bg-red-500 text-white p-0.5 rounded-bl-lg">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </div>

                                                            <div class="w-8 h-8 rounded flex items-center justify-center mr-3 transition-colors"
                                                                :class="isFileRejected(key) ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500 group-hover:bg-bedas-50 group-hover:text-bedas-600'">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                            </div>
                                                            <div class="overflow-hidden">
                                                                <p class="text-xs font-bold uppercase" 
                                                                    :class="isFileRejected(key) ? 'text-red-700' : 'text-slate-700'"
                                                                    x-text="key.replace('_', ' ').replace('ktp', 'KTP').replace('kk', 'KK')"></p>
                                                                <p class="text-[10px] truncate" :class="isFileRejected(key) ? 'text-red-600' : 'text-slate-400'" x-text="isFileRejected(key) ? 'Berkas Ditolak' : 'Klik untuk lihat'"></p>
                                                            </div>
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Metadata Display for Izin Keramaian -->
                                <template x-if="selectedPermohonan.jenis_layanan === 'Izin Keramaian' && selectedPermohonan.metadata">
                                    <div class="space-y-4">
                                         <!-- Data Pemohon -->
                                         <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                             <div class="flex justify-between items-center mb-3">
                                                 <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                                     <span>👤</span> Data Pemohon
                                                 </h4>
                                                 <label class="flex items-center gap-1 cursor-pointer">
                                                     <input type="checkbox" value="Data Pemohon" x-model="invalidItems" class="rounded text-red-500 focus:ring-red-500 border-slate-300 w-3.5 h-3.5">
                                                     <span class="text-xs text-red-500 font-medium">Tidak Sesuai</span>
                                                 </label>
                                             </div>
                                             <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                                 <p><span class="text-slate-400 w-24 inline-block">Nama:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.pemohon?.nama"></span></p>
                                                 <p><span class="text-slate-400 w-24 inline-block">NIK:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.pemohon?.nik"></span></p>
                                                 <p><span class="text-slate-400 w-24 inline-block">TTL:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.pemohon?.ttl"></span></p>
                                                 <p><span class="text-slate-400 w-24 inline-block">Gender:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.pemohon?.gender"></span></p>
                                                 <p><span class="text-slate-400 w-24 inline-block">Pekerjaan:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.pemohon?.pekerjaan"></span></p>
                                                 <div class="md:col-span-2">
                                                     <p><span class="text-slate-400 w-24 inline-block">Alamat:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.pemohon?.alamat"></span></p>
                                                 </div>
                                             </div>
                                         </div>

                                         <!-- Maksud Keramaian -->
                                         <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                                             <div class="flex justify-between items-center mb-3">
                                                 <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                                     <span>🎉</span> Maksud Keramaian
                                                 </h4>
                                                 <label class="flex items-center gap-1 cursor-pointer">
                                                     <input type="checkbox" value="Maksud Keramaian" x-model="invalidItems" class="rounded text-red-500 focus:ring-red-500 border-orange-300 w-3.5 h-3.5">
                                                     <span class="text-xs text-red-500 font-medium">Tidak Sesuai</span>
                                                 </label>
                                             </div>
                                             <div class="space-y-2 text-sm">
                                                 <p><span class="text-slate-500 w-32 inline-block">Hari / Tanggal:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.keramaian?.tanggal"></span></p>
                                                 <p><span class="text-slate-500 w-32 inline-block">Acara:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.keramaian?.acara"></span></p>
                                                 <p><span class="text-slate-500 w-32 inline-block">Lokasi:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.keramaian?.lokasi"></span></p>
                                                 <p><span class="text-slate-500 w-32 inline-block">Hiburan:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.keramaian?.hiburan"></span></p>
                                             </div>
                                         </div>

                                         <!-- File List (Izin Keramaian) -->
                                         <div class="mt-4">
                                             <h4 class="font-bold text-slate-800 mb-3 text-sm">Berkas Lampiran</h4>
                                             <template x-if="selectedPermohonan.metadata.files">
                                                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                     <template x-for="(path, key) in selectedPermohonan.metadata.files" :key="key">
                                                        <button type="button" @click="openFilePreview(path, key)" class="flex items-center p-2 bg-white border rounded-lg hover:shadow-sm transition-all group w-full text-left relative overflow-hidden"
                                                            :class="isFileRejected(key) ? 'border-red-500 bg-red-50' : 'border-slate-200 hover:border-bedas-300'">
                                                            
                                                            <!-- Rejected Badge -->
                                                            <div x-show="isFileRejected(key)" class="absolute top-0 right-0 bg-red-500 text-white p-0.5 rounded-bl-lg">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            </div>

                                                            <div class="w-8 h-8 rounded flex items-center justify-center mr-3 transition-colors"
                                                                :class="isFileRejected(key) ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500 group-hover:bg-bedas-50 group-hover:text-bedas-600'">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                            </div>
                                                            <div class="overflow-hidden">
                                                                <p class="text-xs font-bold uppercase" 
                                                                    :class="isFileRejected(key) ? 'text-red-700' : 'text-slate-700'"
                                                                    x-text="key.replace('_', ' ').replace('ktp', 'KTP')"></p>
                                                                <p class="text-[10px] truncate" :class="isFileRejected(key) ? 'text-red-600' : 'text-slate-400'" x-text="isFileRejected(key) ? 'Berkas Ditolak' : 'Klik untuk lihat'"></p>
                                                            </div>
                                                        </button>
                                                     </template>
                                                 </div>
                                             </template>
                                         </div>
                                    </div>
                                </template>

                                 <!-- Metadata Display for Rekomendasi Bantuan -->
                                 <template x-if="selectedPermohonan.jenis_layanan === 'Rekomendasi Bantuan' && selectedPermohonan.metadata">
                                     <div class="space-y-4">
                                         <!-- Data Rekomendasi -->
                                         <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                             <div class="flex justify-between items-center mb-3">
                                                 <h4 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                                     <span>📋</span> Data Rekomendasi Bantuan
                                                 </h4>
                                                 <label class="flex items-center gap-1 cursor-pointer">
                                                     <input type="checkbox" value="Data Rekomendasi Bantuan" x-model="invalidItems" class="rounded text-red-500 focus:ring-red-500 border-slate-300 w-3.5 h-3.5">
                                                     <span class="text-xs text-red-500 font-medium">Tidak Sesuai</span>
                                                 </label>
                                             </div>
                                             <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                                 <p><span class="text-slate-400 w-28 inline-block">Jenis Kelp:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.rekomendasi?.jenis_kelompok"></span></p>
                                                 <p><span class="text-slate-400 w-28 inline-block">Nama Kelp:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.rekomendasi?.nama_kelompok"></span></p>
                                                 <div class="md:col-span-2">
                                                     <p><span class="text-slate-400 w-28 inline-block">Alamat:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.rekomendasi?.alamat"></span></p>
                                                 </div>
                                                 <p><span class="text-slate-400 w-28 inline-block">Perihal:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.rekomendasi?.perihal"></span></p>
                                                 <p><span class="text-slate-400 w-28 inline-block">Nama Desa:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.rekomendasi?.nama_desa"></span></p>
                                             </div>
                                         </div>

                                         <!-- File List (Rekomendasi Bantuan) -->
                                         <div class="mt-4">
                                             <h4 class="font-bold text-slate-800 mb-3 text-sm">Berkas Lampiran</h4>
                                             <template x-if="selectedPermohonan.metadata.files">
                                                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                     <template x-for="(path, key) in selectedPermohonan.metadata.files" :key="key">
                                                         <button type="button" @click="openFilePreview(path, key)" class="flex items-center p-2 bg-white border rounded-lg hover:shadow-sm transition-all group w-full text-left relative overflow-hidden"
                                                             :class="isFileRejected(key) ? 'border-red-500 bg-red-50' : 'border-slate-200 hover:border-bedas-300'">
                                                             
                                                             <!-- Rejected Badge -->
                                                             <div x-show="isFileRejected(key)" class="absolute top-0 right-0 bg-red-500 text-white p-0.5 rounded-bl-lg">
                                                                 <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                             </div>

                                                             <div class="w-8 h-8 rounded flex items-center justify-center mr-3 transition-colors"
                                                                 :class="isFileRejected(key) ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-500 group-hover:bg-bedas-50 group-hover:text-bedas-600'">
                                                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                             </div>
                                                             <div class="overflow-hidden">
                                                                 <p class="text-xs font-bold uppercase" 
                                                                     :class="isFileRejected(key) ? 'text-red-700' : 'text-slate-700'"
                                                                     x-text="key.replace('_', ' ')"></p>
                                                                 <p class="text-[10px] truncate" :class="isFileRejected(key) ? 'text-red-600' : 'text-slate-400'" x-text="isFileRejected(key) ? 'Berkas Ditolak' : 'Klik untuk lihat'"></p>
                                                             </div>
                                                         </button>
                                                     </template>
                                                 </div>
                                             </template>
                                         </div>
                                     </div>
                                 </template>

                                 <!-- Standard File Link for other services -->
                                 <template x-if="selectedPermohonan.jenis_layanan !== 'Dispen Nikah' && selectedPermohonan.jenis_layanan !== 'Izin Keramaian' && selectedPermohonan.jenis_layanan !== 'Rekomendasi Bantuan' && selectedPermohonan.file_path">
                                    <div class="mt-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Berkas Persyaratan</label>
                                        <button type="button" @click="openFilePreview(selectedPermohonan.file_path, 'main_file')" 
                                            class="w-full flex items-center p-3 border rounded-lg hover:shadow-sm transition-all text-left relative overflow-hidden"
                                            :class="isFileRejected('main_file') ? 'border-red-500 bg-red-50' : 'border-slate-200 hover:border-bedas-300 bg-white'">
                                            
                                            <div x-show="isFileRejected('main_file')" class="absolute top-0 right-0 bg-red-500 text-white p-0.5 rounded-bl-lg">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </div>

                                            <div class="mr-3" :class="isFileRejected('main_file') ? 'text-red-600' : 'text-bedas-600'">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <span class="text-sm font-bold block" :class="isFileRejected('main_file') ? 'text-red-700' : 'text-slate-800'">Lihat Dokumen</span>
                                                <span class="text-xs" :class="isFileRejected('main_file') ? 'text-red-600' : 'text-slate-500'" x-text="isFileRejected('main_file') ? 'Berkas Ditolak' : 'Klik untuk preview'"></span>
                                            </div>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex flex-col items-end border-t border-slate-200">
                         <form method="POST" :action="'/petugas/validate/' + selectedPermohonan.id" class="w-full">
                            @csrf
                            <input type="hidden" name="action" :value="invalidItems.length > 0 ? 'reject' : 'approve'">
                            <input type="hidden" id="detail_invalid_items_json" name="invalid_items_json" :value="JSON.stringify(invalidItems)">
                            
                            <div x-show="invalidItems.length > 0" x-collapse class="w-full mb-4">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Penolakan Tambahan (Opsional)</label>
                                <textarea name="keterangan" rows="2" class="w-full border-slate-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-sm" placeholder="Tambahkan pesan khusus jika diperlukan..."></textarea>
                            </div>
                            
                            <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 w-full">
                                <button type="button" @click="showDetailModal = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-slate-300 px-5 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                    Tutup
                                </button>
                                <button type="submit" @click="const el = document.getElementById('detail_invalid_items_json'); if(el) el.value = JSON.stringify(invalidItems)"
                                        class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent px-5 py-2 text-sm font-semibold text-white shadow-sm transition-all"
                                        :class="invalidItems.length > 0 ? 'bg-red-600 hover:bg-red-700' : 'bg-bedas-600 hover:bg-bedas-700'"
                                        x-text="invalidItems.length > 0 ? 'Tolak Permohonan (' + invalidItems.length + ' item)' : 'Setujui & Teruskan ke Camat'">
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Preview Modal -->
        <div x-show="showFileModal" class="fixed inset-0 overflow-y-auto" style="z-index: 9999; display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
                
                <div x-show="showFileModal" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showFileModal = false">
                    <div class="absolute inset-0 bg-slate-900/75 backdrop-blur-sm"></div>
                </div>

                <div x-show="showFileModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     class="inline-block bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-5xl h-[85vh] flex flex-col relative">
                    
                    <!-- Toolbar -->
                    <div class="bg-white border-b border-slate-100 px-6 py-4 flex justify-between items-center shrink-0">
                        <div class="flex items-center gap-4">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <span class="bg-bedas-50 text-bedas-600 p-1.5 rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></span>
                                Preview Berkas
                            </h3>
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-mono font-bold uppercase" x-text="activeFileKey?.replace(/_/g, ' ').replace('ktp', 'KTP').replace('kk', 'KK')"></span>
                        </div>
                        <button @click="showFileModal = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-slate-50 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 bg-slate-50 relative overflow-hidden flex items-center justify-center p-6">
                        <template x-if="activeFileType === 'image'">
                            <div class="relative w-full h-full flex items-center justify-center">
                                <img :src="activeFileSrc" class="max-w-full max-h-full object-contain rounded-lg shadow-sm" alt="Preview">
                            </div>
                        </template>
                        <template x-if="activeFileType === 'pdf'">
                            <iframe :src="activeFileSrc" class="w-full h-full rounded-lg shadow-sm border border-slate-200 bg-white" frameborder="0"></iframe>
                        </template>
                    </div>

                    <!-- Footer Actions -->
                    <div class="bg-white border-t border-slate-100 px-6 py-4 flex justify-end gap-3 shrink-0">
                        <button type="button" @click="showFileModal = false" class="px-6 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition-colors">
                            Tutup
                        </button>
                        <button type="button" @click="toggleFileReject()" 
                                class="px-6 py-2.5 rounded-xl text-white font-bold shadow-lg transition-colors flex items-center gap-2"
                                :class="isFileRejected() ? 'bg-slate-500 hover:bg-slate-600 shadow-slate-200' : 'bg-red-600 hover:bg-red-700 shadow-red-200'">
                            
                            <template x-if="isFileRejected()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </template>
                            <template x-if="!isFileRejected()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </template>
                            
                            <span x-text="isFileRejected() ? 'Batalkan Tolak' : 'Tolak Berkas'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nomor Surat Modal -->
        <div x-show="showNomorModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showNomorModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showNomorModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <form x-bind:action="'/petugas/nomor-surat/' + nomorSuratId" method="POST">
                        @csrf
                        
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-indigo-100 text-indigo-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                            </div>
                            
                            <div class="text-center">
                                <h3 class="text-xl leading-6 font-bold text-slate-900 mb-2">Penomoran Surat</h3>
                                <p class="text-sm text-slate-500 mb-6">Dokumen telah ditandatangani Camat. Masukkan nomor surat untuk menyelesaikan proses.</p>
                                
                                <div class="mt-4 text-left">
                                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wide mb-2">Nomor Surat</label>
                                    <input type="text" name="nomor_surat" class="w-full px-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Contoh: 145/001/DS/2026" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" 
                                class="w-full inline-flex justify-center rounded-xl border border-transparent px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto transition-colors">
                                Simpan & Selesaikan
                            </button>
                            <button type="button" @click="showNomorModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

