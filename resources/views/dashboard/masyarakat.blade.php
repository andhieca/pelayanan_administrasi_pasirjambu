<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-3xl text-slate-900 leading-tight tracking-tight">
                    {{ __('Dashboard') }} <span class="text-bedas-600">Masyarakat</span>
                </h2>
                <p class="text-slate-500 text-sm mt-1 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Selamat datang kembali di pusat pelayanan digital masyarakat.
                </p>
            </div>
            <div class="hidden lg:flex items-center gap-3 px-4 py-2 bg-white rounded-2xl shadow-sm border border-slate-100">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider tabular-nums">Sistem Online : {{ now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        activeTab: 'create',
        showForm: false, 
        selectedLayanan: '', 
        isEdit: false, 
        editId: null, 
        showDeleteModal: false, 
        deleteId: null, 
        showDetailModal: false, 
        selectedPermohonan: null,
        filePreview: null,
        fileName: null,
        filePreviews: {},
        
        // Preview Modal State
        showPreviewModal: false,
        previewUrl: '',
        previewType: '', // 'image' or 'pdf'
        existingFiles: {}, // Metadata for existing uploaded files
        isDraftValue: 0,
        rejectedItems: [],

        parseItems(raw) {
            if (!raw) return [];
            if (Array.isArray(raw)) return raw;
            if (typeof raw === 'string') {
                try {
                    const parsed = JSON.parse(raw);
                    if (Array.isArray(parsed)) return parsed;
                    if (parsed && typeof parsed === 'object') return Object.values(parsed);
                } catch(e) {
                    return [raw];
                }
            }
            if (typeof raw === 'object') return Object.values(raw);
            return [];
        },

        isRejected(label) {
            if (!this.rejectedItems || !Array.isArray(this.rejectedItems)) return false;
            const target = String(label).trim().toLowerCase();
            return this.rejectedItems.some(item => String(item).trim().toLowerCase() === target);
        },

        isDetailRejected(label) {
            if (!this.selectedPermohonan || this.selectedPermohonan.status !== 'ditolak') return false;
            const items = this.parseItems(this.selectedPermohonan.invalid_items);
            if (!items || items.length === 0) return false;
            
            const target = String(label).trim().toLowerCase();
            return items.some(item => String(item).trim().toLowerCase() === target);
        },

        viewFile(path) {
            this.previewUrl = '/berkas/' + path;
            const extension = path.split('.').pop().toLowerCase();
            this.previewType = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension) ? 'image' : 'pdf';
            this.showPreviewModal = true;
        },
        viewPrint(url) {
            this.previewUrl = url;
            this.previewType = 'pdf';
            this.showPreviewModal = true;
        },
        
        // Metadata Structure
        suami: { nama: '', nik: '', bin: '', ttl: '', agama: '', pekerjaan: '', status: '', alamat: '' },
        istri: { nama: '', nik: '', binti: '', ttl: '', agama: '', pekerjaan: '', status: '', alamat: '' },
        pernikahan: { hari: '', tanggal: '', waktu: '', tempat: '' },
        pemohon: { nama: '', ttl: '', gender: '', nik: '', pekerjaan: '', alamat: '' },
        keramaian: { tanggal: '', acara: '', lokasi: '', hiburan: '' },
        rekomendasi: { jenis_kelompok: '', nama_kelompok: '', alamat: '', perihal: '', nama_desa: '' },
        alasan: '',
        whatsapp: '',
        validationErrors: {},
        formSubmitted: false,
        emptyFields: {},

        // Input sanitization helpers
        sanitizeNama(value) {
            return value.replace(/[^a-zA-Z\s\.\',]/g, '');
        },
        sanitizeNik(value) {
            return value.replace(/[^0-9]/g, '').substring(0, 16);
        },
        nikBirthDate(nik) {
            let dd = parseInt(nik.substring(6, 8));
            if (dd >= 41) dd -= 40; // Perempuan: tanggal lahir + 40
            return String(dd).padStart(2, '0') + '-' + nik.substring(8, 10) + '-' + nik.substring(10, 12);
        },
        nikErrors: {},
        validateNik(nik, fieldKey) {
            if (!nik || nik.length === 0) {
                delete this.nikErrors[fieldKey];
                return;
            }
            // Must be exactly 16 digits
            if (nik.length !== 16) {
                this.nikErrors[fieldKey] = 'NIK harus tepat 16 digit (' + nik.length + '/16)';
                return;
            }
            // 6 digit pertama: kode wilayah (01-99 untuk masing-masing)
            const kodeProv = parseInt(nik.substring(0, 2));
            const kodeKab = parseInt(nik.substring(2, 4));
            const kodeKec = parseInt(nik.substring(4, 6));
            if (kodeProv < 1 || kodeProv > 99) {
                this.nikErrors[fieldKey] = 'Kode provinsi tidak valid (digit 1-2)';
                return;
            }
            if (kodeKab < 1 || kodeKab > 99) {
                this.nikErrors[fieldKey] = 'Kode kabupaten/kota tidak valid (digit 3-4)';
                return;
            }
            if (kodeKec < 1 || kodeKec > 99) {
                this.nikErrors[fieldKey] = 'Kode kecamatan tidak valid (digit 5-6)';
                return;
            }
            // 6 digit kedua: tanggal lahir DDMMYY (perempuan DD+40)
            const tanggal = parseInt(nik.substring(6, 8));
            const bulan = parseInt(nik.substring(8, 10));
            // Tanggal: 01-31 (laki-laki) atau 41-71 (perempuan, +40)
            if (!((tanggal >= 1 && tanggal <= 31) || (tanggal >= 41 && tanggal <= 71))) {
                this.nikErrors[fieldKey] = 'Tanggal lahir tidak valid (digit 7-8). Laki-laki: 01-31, Perempuan: 41-71';
                return;
            }
            if (bulan < 1 || bulan > 12) {
                this.nikErrors[fieldKey] = 'Bulan lahir tidak valid (digit 9-10). Harus 01-12';
                return;
            }
            // 4 digit terakhir: nomor urut (0001-9999)
            const nomorUrut = parseInt(nik.substring(12, 16));
            if (nomorUrut < 1) {
                this.nikErrors[fieldKey] = 'Nomor urut tidak valid (digit 13-16). Harus mulai dari 0001';
                return;
            }
            // Valid!
            delete this.nikErrors[fieldKey];
        },
        sanitizePhone(value) {
            return value.replace(/[^0-9]/g, '').substring(0, 15);
        },
        sanitizePekerjaan(value) {
            return value.replace(/[^a-zA-Z\s\/\-]/g, '');
        },
        sanitizeTtl(value) {
            return value.replace(/[^a-zA-Z\s,0-9]/g, '');
        },
        sanitizeNamaKelompok(value) {
            return value.replace(/[^a-zA-Z0-9\s\.\-]/g, '');
        },
        sanitizeJenisKelompok(value) {
            return value.replace(/[^a-zA-Z\s\/\-]/g, '');
        },
        sanitizeNamaDesa(value) {
            return value.replace(/[^a-zA-Z\s]/g, '');
        },
        sanitizeAcara(value) {
            return value.replace(/[^a-zA-Z\s\/\-]/g, '');
        },

        // Check if a field is empty
        isEmpty(value) {
            return !value || String(value).trim() === '';
        },

        // Validate all required fields based on selected layanan
        validateForm() {
            this.formSubmitted = true;
            this.emptyFields = {};
            let isValid = true;

            if (this.selectedLayanan === 'Dispen Nikah') {
                // Calon Suami
                const suamiFields = {
                    'suami.nama': { value: this.suami.nama, label: 'Nama Suami' },
                    'suami.nik': { value: this.suami.nik, label: 'NIK Suami' },
                    'suami.bin': { value: this.suami.bin, label: 'Bin Suami' },
                    'suami.ttl': { value: this.suami.ttl, label: 'TTL Suami' },
                    'suami.agama': { value: this.suami.agama, label: 'Agama Suami' },
                    'suami.pekerjaan': { value: this.suami.pekerjaan, label: 'Pekerjaan Suami' },
                    'suami.status': { value: this.suami.status, label: 'Status Suami' },
                    'suami.alamat': { value: this.suami.alamat, label: 'Alamat Suami' },
                };
                // Calon Istri
                const istriFields = {
                    'istri.nama': { value: this.istri.nama, label: 'Nama Istri' },
                    'istri.nik': { value: this.istri.nik, label: 'NIK Istri' },
                    'istri.binti': { value: this.istri.binti, label: 'Binti Istri' },
                    'istri.ttl': { value: this.istri.ttl, label: 'TTL Istri' },
                    'istri.agama': { value: this.istri.agama, label: 'Agama Istri' },
                    'istri.pekerjaan': { value: this.istri.pekerjaan, label: 'Pekerjaan Istri' },
                    'istri.status': { value: this.istri.status, label: 'Status Istri' },
                    'istri.alamat': { value: this.istri.alamat, label: 'Alamat Istri' },
                };
                // Pernikahan
                const pernikahanFields = {
                    'pernikahan.hari': { value: this.pernikahan.hari, label: 'Hari Pernikahan' },
                    'pernikahan.tanggal': { value: this.pernikahan.tanggal, label: 'Tanggal Pernikahan' },
                    'pernikahan.waktu': { value: this.pernikahan.waktu, label: 'Waktu Pernikahan' },
                    'pernikahan.tempat': { value: this.pernikahan.tempat, label: 'Tempat Akad' },
                };
                // Lainnya
                const lainnyaFields = {
                    'alasan': { value: this.alasan, label: 'Alasan' },
                    'whatsapp': { value: this.whatsapp, label: 'Nomor WhatsApp' },
                };

                const allFields = { ...suamiFields, ...istriFields, ...pernikahanFields, ...lainnyaFields };
                for (const [key, field] of Object.entries(allFields)) {
                    if (this.isEmpty(field.value)) {
                        this.emptyFields[key] = field.label + ' wajib diisi';
                        isValid = false;
                    }
                }

                // Extra: NIK Validation
                if (this.suami.nik) {
                    this.validateNik(this.suami.nik, 'suami');
                    if (this.nikErrors['suami']) isValid = false;
                }
                if (this.istri.nik) {
                    this.validateNik(this.istri.nik, 'istri');
                    if (this.nikErrors['istri']) isValid = false;
                }
                // Extra: WhatsApp format
                if (this.whatsapp && !/^(08|628)[0-9]{8,13}$/.test(this.whatsapp)) {
                    this.emptyFields['whatsapp'] = 'Format WhatsApp tidak valid';
                    isValid = false;
                }

            } else if (this.selectedLayanan === 'Izin Keramaian') {
                const fields = {
                    'pemohon.nama': { value: this.pemohon.nama, label: 'Nama Pemohon' },
                    'pemohon.nik': { value: this.pemohon.nik, label: 'NIK Pemohon' },
                    'pemohon.ttl': { value: this.pemohon.ttl, label: 'TTL Pemohon' },
                    'pemohon.gender': { value: this.pemohon.gender, label: 'Jenis Kelamin' },
                    'pemohon.pekerjaan': { value: this.pemohon.pekerjaan, label: 'Pekerjaan' },
                    'pemohon.alamat': { value: this.pemohon.alamat, label: 'Alamat' },
                    'keramaian.tanggal': { value: this.keramaian.tanggal, label: 'Hari/Tanggal Keramaian' },
                    'keramaian.acara': { value: this.keramaian.acara, label: 'Acara' },
                    'keramaian.lokasi': { value: this.keramaian.lokasi, label: 'Lokasi' },
                    'keramaian.hiburan': { value: this.keramaian.hiburan, label: 'Hiburan' },
                };
                for (const [key, field] of Object.entries(fields)) {
                    if (this.isEmpty(field.value)) {
                        this.emptyFields[key] = field.label + ' wajib diisi';
                        isValid = false;
                    }
                }
                if (this.pemohon.nik) {
                    this.validateNik(this.pemohon.nik, 'pemohon');
                    if (this.nikErrors['pemohon']) isValid = false;
                }

            } else if (this.selectedLayanan === 'Rekomendasi Bantuan') {
                const fields = {
                    'rekomendasi.jenis_kelompok': { value: this.rekomendasi.jenis_kelompok, label: 'Jenis Kelompok' },
                    'rekomendasi.nama_kelompok': { value: this.rekomendasi.nama_kelompok, label: 'Nama Kelompok' },
                    'rekomendasi.alamat': { value: this.rekomendasi.alamat, label: 'Alamat Kelompok' },
                    'rekomendasi.perihal': { value: this.rekomendasi.perihal, label: 'Perihal' },
                    'rekomendasi.nama_desa': { value: this.rekomendasi.nama_desa, label: 'Nama Desa' },
                };
                for (const [key, field] of Object.entries(fields)) {
                    if (this.isEmpty(field.value)) {
                        this.emptyFields[key] = field.label + ' wajib diisi';
                        isValid = false;
                    }
                }
            }

            // Scroll to first error
            if (!isValid) {
                this.$nextTick(() => {
                    const firstError = document.querySelector('.validation-warning');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }

            return isValid;
        },

        // Validation checker (format validation)
        validateField(field, value) {
            let error = '';
            switch(field) {
                case 'nik':
                    if (value && value.length !== 16) error = 'NIK harus tepat 16 digit';
                    break;
                case 'whatsapp':
                    if (value && !/^(08|628)[0-9]{8,13}$/.test(value)) error = 'Format: 08xxxxxxxxxx';
                    break;
                case 'nama':
                    if (value && !/^[a-zA-Z\s\.\',]+$/.test(value)) error = 'Hanya huruf, spasi, titik';
                    break;
            }
            this.validationErrors[field] = error;
            return error;
        },

        handleFileUpload(event, key) {
            const file = event.target.files[0];
            if (file) {
                if (key === 'main') {
                    this.fileName = file.name;
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => { this.filePreview = e.target.result; };
                        reader.readAsDataURL(file);
                    } else {
                        this.filePreview = null;
                    }
                } else {
                    // For multi-upload: Update filePreviews if strictly needed, 
                    // but we mainly rely on existingFiles for Edit mode display.
                    // Ideally we could also show the NEWly selected file name here.
                     this.filePreviews[key] = { name: file.name };
                }
            }
        },
        resetForm() {
            this.showForm = false;
            this.isEdit = false;
            this.editId = null;
            this.selectedLayanan = '';
            this.filePreview = null;
            this.fileName = null;
            this.filePreviews = {};
            this.existingFiles = {};
            this.suami = { nama: '', nik: '', bin: '', ttl: '', agama: '', pekerjaan: '', status: '', alamat: '' };
            this.istri = { nama: '', nik: '', binti: '', ttl: '', agama: '', pekerjaan: '', status: '', alamat: '' };
            this.pernikahan = { hari: '', tanggal: '', waktu: '', tempat: '' };
            this.pemohon = { nama: '', ttl: '', gender: '', nik: '', pekerjaan: '', alamat: '' };
            this.keramaian = { tanggal: '', acara: '', lokasi: '', hiburan: '' };
            this.rekomendasi = { jenis_kelompok: '', nama_kelompok: '', alamat: '', perihal: '', nama_desa: '' };
            this.alasan = '';
            this.whatsapp = '';
            this.isDraftValue = 0;
            this.rejectedItems = [];
        },
        editPermohonan(item) {
            this.activeTab = 'create';
            this.showForm = true;
            this.isEdit = true;
            this.editId = item.id;
            this.selectedLayanan = item.jenis_layanan;
            
            // Populate metadata
            if (item.metadata) {
                if (item.metadata.suami) this.suami = { ...this.suami, ...item.metadata.suami };
                if (item.metadata.istri) this.istri = { ...this.istri, ...item.metadata.istri };
                if (item.metadata.pernikahan) this.pernikahan = { ...this.pernikahan, ...item.metadata.pernikahan };
                if (item.metadata.pemohon) this.pemohon = { ...this.pemohon, ...item.metadata.pemohon };
                if (item.metadata.keramaian) this.keramaian = { ...this.keramaian, ...item.metadata.keramaian };
                if (item.metadata.rekomendasi) this.rekomendasi = { ...this.rekomendasi, ...item.metadata.rekomendasi };
                this.alasan = item.metadata.alasan || '';
                this.whatsapp = item.metadata.whatsapp || '';
                this.existingFiles = item.metadata.files || {};
            }
            this.rejectedItems = this.parseItems(item.invalid_items);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row gap-6">
                <!-- Sidebar -->
                <div class="w-full md:w-72 flex-shrink-0">
                    <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-sm sm:rounded-3xl border border-slate-100 sticky top-24">
                        <div class="p-6 space-y-3">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mb-2">Menu Utama</p>
                            
                            <button @click="activeTab = 'create'" 
                                :class="activeTab === 'create' ? 'bg-bedas-500 text-white shadow-lg shadow-bedas-200' : 'text-slate-600 hover:bg-slate-50'"
                                class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl transition-all duration-300 font-bold text-left group">
                                <div :class="activeTab === 'create' ? 'bg-white/20' : 'bg-bedas-50 text-bedas-600 group-hover:bg-bedas-100'" class="p-2 rounded-xl transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                                Buat Permohonan
                            </button>

                            <button @click="activeTab = 'history'" 
                                :class="activeTab === 'history' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200' : 'text-slate-600 hover:bg-slate-50'"
                                class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl transition-all duration-300 font-bold text-left group">
                                <div :class="activeTab === 'history' ? 'bg-white/20' : 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100'" class="p-2 rounded-xl transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                Riwayat Permohonan
                            </button>

                            <div class="pt-4 mt-4 border-t border-slate-50">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mb-2">Simpanan</p>
                                <button @click="activeTab = 'draft'" 
                                    :class="activeTab === 'draft' ? 'bg-amber-500 text-white shadow-lg shadow-amber-200' : 'text-slate-600 hover:bg-slate-50'"
                                    class="w-full flex items-center gap-3 px-5 py-4 rounded-2xl transition-all duration-300 font-bold text-left group">
                                    <div :class="activeTab === 'draft' ? 'bg-white/20' : 'bg-amber-50 text-amber-600 group-hover:bg-amber-100'" class="p-2 rounded-xl transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </div>
                                    Draft Permohonan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1">

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-bold">Terdapat kesalahan pada inputan Anda:</span>
                            </div>
                            <ul class="list-disc list-inside text-sm ml-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Success/Error Message -->
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                            class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2"
                            role="alert">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="block sm:inline font-medium">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Create Application Tab -->
                    <div x-show="activeTab === 'create'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                        
                        <!-- Service Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" x-show="!showForm"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100">
                            <!-- Card 1 -->
                            <div @click="showForm = true; selectedLayanan = 'Dispen Nikah'; isEdit = false;"
                                class="cursor-pointer group bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:border-blue-100 transition-all duration-300 transform hover:-translate-y-1">
                                <div
                                    class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                                    💍
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 mb-1">Dispen Nikah</h3>
                                <p class="text-slate-500 text-sm">Pelayanan pengajuan surat dispensasi nikah.</p>
                            </div>
                            <!-- Card 2 -->
                            <div @click="showForm = true; selectedLayanan = 'Rekomendasi Bantuan'; isEdit = false;"
                                class="cursor-pointer group bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:border-emerald-100 transition-all duration-300 transform hover:-translate-y-1">
                                <div
                                    class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                                    🤝
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 mb-1">Rekomendasi Bantuan</h3>
                                <p class="text-slate-500 text-sm">Pelayanan surat rekomendasi bantuan sosial.</p>
                            </div>
                            <!-- Card 3 -->
                            <div @click="showForm = true; selectedLayanan = 'Izin Keramaian'; isEdit = false;"
                                class="cursor-pointer group bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:border-violet-100 transition-all duration-300 transform hover:-translate-y-1">
                                <div
                                    class="w-12 h-12 bg-violet-50 text-violet-600 rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                                    🎉
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 mb-1">Izin Keramaian</h3>
                                <p class="text-slate-500 text-sm">Pelayanan perizinan acara keramaian warga.</p>
                            </div>
                        </div>

                        <!-- Input Form (Create & Edit) -->
                        <div x-show="showForm" x-transition
                            class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8 overflow-hidden"
                            style="display: none;">
                            <div class="p-8">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-slate-800"><span
                                            x-text="isEdit ? 'Ubah Permohonan' : 'Form Pengajuan'"></span>: <span
                                            x-text="selectedLayanan" class="text-bedas-600"></span></h3>
                                    <button @click="resetForm()" class="text-slate-400 hover:text-slate-600 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <form :action="isEdit ? '{{ url('masyarakat/permohonan') }}/' + editId : '{{ route('masyarakat.store') }}'" 
                                    @submit.prevent="isDraftValue == 1 || validateForm() ? $el.submit() : null"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <template x-if="isEdit">
                                        <input type="hidden" name="_method" value="PUT">
                                    </template>
                                    <input type="hidden" name="jenis_layanan" x-model="selectedLayanan">

                                    <!-- Conditional Input for Dispen Nikah -->
                                    <div x-show="selectedLayanan == 'Dispen Nikah'" class="space-y-8 mb-8">
                                        
                                        <!-- Calon Suami -->
                                        <div class="p-6 rounded-2xl border" :class="isRejected('Data Calon Suami') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-slate-50 border-slate-100'">
                                            <div x-show="isRejected('Data Calon Suami')" class="text-xs font-bold text-red-600 mb-3 flex items-center gap-1 bg-red-100/50 w-fit px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                Bagian ini perlu diperbaiki
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm">👨</span>
                                                Data Calon Suami
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Nama Lengkap</label>
                                                    <input type="text" name="suami[nama]" x-model="suami.nama" @input="suami.nama = sanitizeNama($event.target.value); emptyFields['suami.nama'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['suami.nama']}" placeholder="Sesuai KTP">
                                                    <p class="text-xs mt-1" :class="formSubmitted && emptyFields['suami.nama'] ? 'text-red-500 validation-warning' : 'text-slate-400'" x-text="formSubmitted && emptyFields['suami.nama'] ? emptyFields['suami.nama'] : 'Hanya huruf, spasi, titik, dan apostrof'"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">NIK</label>
                                                    <input type="text" name="suami[nik]" x-model="suami.nik" @input="suami.nik = sanitizeNik($event.target.value); validateNik(suami.nik, 'suami'); emptyFields['suami.nik'] = null" maxlength="16" minlength="16" inputmode="numeric" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm font-mono tracking-wider" :class="{'border-red-500 ring-1 ring-red-500': (formSubmitted && emptyFields['suami.nik']) || nikErrors['suami'], 'border-emerald-500 ring-1 ring-emerald-500': suami.nik && suami.nik.length === 16 && !nikErrors['suami']}" placeholder="Contoh: 3204XXXXDDMMYYXXXX">
                                                    <!-- Progress bar -->
                                                    <div x-show="suami.nik && suami.nik.length > 0 && suami.nik.length < 16" class="mt-1">
                                                        <div class="w-full bg-slate-200 rounded-full h-1">
                                                            <div class="bg-bedas-500 h-1 rounded-full transition-all" :style="'width:' + (suami.nik.length / 16 * 100) + '%'"></div>
                                                        </div>
                                                        <p class="text-[10px] text-slate-400 mt-0.5"><span x-text="suami.nik.length"></span>/16 digit</p>
                                                    </div>
                                                    <!-- Format breakdown when 16 digits -->
                                                    <div x-show="suami.nik && suami.nik.length === 16 && !nikErrors['suami']" class="mt-1 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                                        <p class="text-[10px] text-emerald-600 font-medium">NIK Valid — <span class="text-slate-400" x-text="'Wilayah: ' + suami.nik.substring(0,2) + '.' + suami.nik.substring(2,4) + '.' + suami.nik.substring(4,6) + ' | Lahir: ' + nikBirthDate(suami.nik) + ' | Urut: ' + suami.nik.substring(12,16)"></span></p>
                                                    </div>
                                                    <!-- Error messages -->
                                                    <p x-show="nikErrors['suami']" class="text-xs text-red-500 mt-1 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> <span x-text="nikErrors['suami']"></span></p>
                                                    <p x-show="formSubmitted && emptyFields['suami.nik'] && !nikErrors['suami']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['suami.nik']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Bin</label>
                                                    <input type="text" name="suami[bin]" x-model="suami.bin" @input="suami.bin = sanitizeNama($event.target.value); emptyFields['suami.bin'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['suami.bin']}" placeholder="Nama Ayah">
                                                    <p x-show="formSubmitted && emptyFields['suami.bin']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['suami.bin']"></p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Tempat, Tanggal Lahir</label>
                                                    <input type="text" name="suami[ttl]" x-model="suami.ttl" @input="suami.ttl = sanitizeTtl($event.target.value); emptyFields['suami.ttl'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['suami.ttl']}" placeholder="Contoh: Bandung, 12 Januari 1995">
                                                    <p x-show="formSubmitted && emptyFields['suami.ttl']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['suami.ttl']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Agama</label>
                                                    <select name="suami[agama]" x-model="suami.agama" @change="emptyFields['suami.agama'] = null" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['suami.agama']}">
                                                        <option value="">Pilih Agama</option>
                                                        <option value="Islam">Islam</option>
                                                        <option value="Kristen">Kristen</option>
                                                        <option value="Katolik">Katolik</option>
                                                        <option value="Hindu">Hindu</option>
                                                        <option value="Buddha">Buddha</option>
                                                        <option value="Konghucu">Konghucu</option>
                                                    </select>
                                                    <p x-show="formSubmitted && emptyFields['suami.agama']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['suami.agama']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Pekerjaan</label>
                                                    <input type="text" name="suami[pekerjaan]" x-model="suami.pekerjaan" @input="suami.pekerjaan = sanitizePekerjaan($event.target.value); emptyFields['suami.pekerjaan'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['suami.pekerjaan']}" placeholder="Pekerjaan saat ini">
                                                    <p x-show="formSubmitted && emptyFields['suami.pekerjaan']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['suami.pekerjaan']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Status</label>
                                                    <select name="suami[status]" x-model="suami.status" @change="emptyFields['suami.status'] = null" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['suami.status']}">
                                                        <option value="">Pilih Status</option>
                                                        <option value="Belum Kawin">Belum Kawin</option>
                                                        <option value="Duda (Cerai Hidup)">Duda (Cerai Hidup)</option>
                                                        <option value="Duda (Cerai Mati)">Duda (Cerai Mati)</option>
                                                    </select>
                                                    <p x-show="formSubmitted && emptyFields['suami.status']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['suami.status']"></p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Alamat</label>
                                                    <textarea name="suami[alamat]" x-model="suami.alamat" @input="emptyFields['suami.alamat'] = null" rows="2" maxlength="500" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['suami.alamat']}" placeholder="Alamat Lengkap"></textarea>
                                                    <div class="flex justify-between items-center mt-1">
                                                        <p x-show="formSubmitted && emptyFields['suami.alamat']" class="text-xs text-red-500 validation-warning" x-text="emptyFields['suami.alamat']"></p>
                                                        <p class="text-xs text-slate-400" :class="{'ml-auto': !(formSubmitted && emptyFields['suami.alamat'])}"><span x-text="suami.alamat.length"></span>/500 karakter</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Calon Istri -->
                                        <div class="p-6 rounded-2xl border" :class="isRejected('Data Calon Istri') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-slate-50 border-slate-100'">
                                            <div x-show="isRejected('Data Calon Istri')" class="text-xs font-bold text-red-600 mb-3 flex items-center gap-1 bg-red-100/50 w-fit px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                Bagian ini perlu diperbaiki
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <span class="w-8 h-8 bg-pink-100 text-pink-600 rounded-lg flex items-center justify-center text-sm">👩</span>
                                                Data Calon Istri
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Nama Lengkap</label>
                                                    <input type="text" name="istri[nama]" x-model="istri.nama" @input="istri.nama = sanitizeNama($event.target.value); emptyFields['istri.nama'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['istri.nama']}" placeholder="Sesuai KTP">
                                                    <p class="text-xs mt-1" :class="formSubmitted && emptyFields['istri.nama'] ? 'text-red-500 validation-warning' : 'text-slate-400'" x-text="formSubmitted && emptyFields['istri.nama'] ? emptyFields['istri.nama'] : 'Hanya huruf, spasi, titik, dan apostrof'"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">NIK</label>
                                                    <input type="text" name="istri[nik]" x-model="istri.nik" @input="istri.nik = sanitizeNik($event.target.value); validateNik(istri.nik, 'istri'); emptyFields['istri.nik'] = null" maxlength="16" minlength="16" inputmode="numeric" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm font-mono tracking-wider" :class="{'border-red-500 ring-1 ring-red-500': (formSubmitted && emptyFields['istri.nik']) || nikErrors['istri'], 'border-emerald-500 ring-1 ring-emerald-500': istri.nik && istri.nik.length === 16 && !nikErrors['istri']}" placeholder="Contoh: 3204XXXXDDMMYYXXXX">
                                                    <!-- Progress bar -->
                                                    <div x-show="istri.nik && istri.nik.length > 0 && istri.nik.length < 16" class="mt-1">
                                                        <div class="w-full bg-slate-200 rounded-full h-1">
                                                            <div class="bg-bedas-500 h-1 rounded-full transition-all" :style="'width:' + (istri.nik.length / 16 * 100) + '%'"></div>
                                                        </div>
                                                        <p class="text-[10px] text-slate-400 mt-0.5"><span x-text="istri.nik.length"></span>/16 digit</p>
                                                    </div>
                                                    <!-- Format breakdown when 16 digits -->
                                                    <div x-show="istri.nik && istri.nik.length === 16 && !nikErrors['istri']" class="mt-1 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                                        <p class="text-[10px] text-emerald-600 font-medium">NIK Valid — <span class="text-slate-400" x-text="'Wilayah: ' + istri.nik.substring(0,2) + '.' + istri.nik.substring(2,4) + '.' + istri.nik.substring(4,6) + ' | Lahir: ' + nikBirthDate(istri.nik) + ' | Urut: ' + istri.nik.substring(12,16)"></span></p>
                                                    </div>
                                                    <!-- Error messages -->
                                                    <p x-show="nikErrors['istri']" class="text-xs text-red-500 mt-1 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> <span x-text="nikErrors['istri']"></span></p>
                                                    <p x-show="formSubmitted && emptyFields['istri.nik'] && !nikErrors['istri']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['istri.nik']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Binti</label>
                                                    <input type="text" name="istri[binti]" x-model="istri.binti" @input="istri.binti = sanitizeNama($event.target.value); emptyFields['istri.binti'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['istri.binti']}" placeholder="Nama Ayah">
                                                    <p x-show="formSubmitted && emptyFields['istri.binti']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['istri.binti']"></p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Tempat, Tanggal Lahir</label>
                                                    <input type="text" name="istri[ttl]" x-model="istri.ttl" @input="istri.ttl = sanitizeTtl($event.target.value); emptyFields['istri.ttl'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['istri.ttl']}" placeholder="Contoh: Bandung, 12 Januari 1995">
                                                    <p x-show="formSubmitted && emptyFields['istri.ttl']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['istri.ttl']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Agama</label>
                                                    <select name="istri[agama]" x-model="istri.agama" @change="emptyFields['istri.agama'] = null" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['istri.agama']}">
                                                        <option value="">Pilih Agama</option>
                                                        <option value="Islam">Islam</option>
                                                        <option value="Kristen">Kristen</option>
                                                        <option value="Katolik">Katolik</option>
                                                        <option value="Hindu">Hindu</option>
                                                        <option value="Buddha">Buddha</option>
                                                        <option value="Konghucu">Konghucu</option>
                                                    </select>
                                                    <p x-show="formSubmitted && emptyFields['istri.agama']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['istri.agama']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Pekerjaan</label>
                                                    <input type="text" name="istri[pekerjaan]" x-model="istri.pekerjaan" @input="istri.pekerjaan = sanitizePekerjaan($event.target.value); emptyFields['istri.pekerjaan'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['istri.pekerjaan']}" placeholder="Pekerjaan saat ini">
                                                    <p x-show="formSubmitted && emptyFields['istri.pekerjaan']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['istri.pekerjaan']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Status</label>
                                                    <select name="istri[status]" x-model="istri.status" @change="emptyFields['istri.status'] = null" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['istri.status']}">
                                                        <option value="">Pilih Status</option>
                                                        <option value="Belum Kawin">Belum Kawin</option>
                                                        <option value="Janda (Cerai Hidup)">Janda (Cerai Hidup)</option>
                                                        <option value="Janda (Cerai Mati)">Janda (Cerai Mati)</option>
                                                    </select>
                                                    <p x-show="formSubmitted && emptyFields['istri.status']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['istri.status']"></p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Alamat</label>
                                                    <textarea name="istri[alamat]" x-model="istri.alamat" @input="emptyFields['istri.alamat'] = null" rows="2" maxlength="500" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['istri.alamat']}" placeholder="Alamat Lengkap"></textarea>
                                                    <div class="flex justify-between items-center mt-1">
                                                        <p x-show="formSubmitted && emptyFields['istri.alamat']" class="text-xs text-red-500 validation-warning" x-text="emptyFields['istri.alamat']"></p>
                                                        <p class="text-xs text-slate-400" :class="{'ml-auto': !(formSubmitted && emptyFields['istri.alamat'])}"><span x-text="istri.alamat.length"></span>/500 karakter</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Rencana Pernikahan -->
                                        <div class="p-6 rounded-2xl border" :class="isRejected('Rencana Pernikahan') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-slate-50 border-slate-100'">
                                            <div x-show="isRejected('Rencana Pernikahan')" class="text-xs font-bold text-red-600 mb-3 flex items-center gap-1 bg-red-100/50 w-fit px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                Bagian ini perlu diperbaiki
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center text-sm">📅</span>
                                                Rencana Pernikahan
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Hari</label>
                                                    <input type="text" name="pernikahan[hari]" x-model="pernikahan.hari" readonly class="w-full border-slate-300 rounded-lg bg-slate-50 cursor-not-allowed text-sm text-slate-500 focus:ring-0 focus:border-slate-300" placeholder="Pilih tanggal dahulu">
                                                    <p x-show="formSubmitted && emptyFields['pernikahan.hari']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['pernikahan.hari']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Tanggal</label>
                                                    <input type="date" name="pernikahan[tanggal]" x-model="pernikahan.tanggal" @input="emptyFields['pernikahan.tanggal'] = null; emptyFields['pernikahan.hari'] = null; if($event.target.value){ const d = new Date($event.target.value); const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; pernikahan.hari = days[d.getDay()]; } else { pernikahan.hari = ''; }" min="{{ date('Y-m-d') }}" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['pernikahan.tanggal']}">
                                                    <p x-show="formSubmitted && emptyFields['pernikahan.tanggal']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['pernikahan.tanggal']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Waktu</label>
                                                    <input type="time" name="pernikahan[waktu]" x-model="pernikahan.waktu" @input="emptyFields['pernikahan.waktu'] = null" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['pernikahan.waktu']}">
                                                    <p x-show="formSubmitted && emptyFields['pernikahan.waktu']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['pernikahan.waktu']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Tempat Akad</label>
                                                    <input type="text" name="pernikahan[tempat]" x-model="pernikahan.tempat" @input="emptyFields['pernikahan.tempat'] = null" maxlength="200" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['pernikahan.tempat']}" placeholder="Nama Masjid / Alamat">
                                                    <p x-show="formSubmitted && emptyFields['pernikahan.tempat']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['pernikahan.tempat']"></p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Lainnya -->
                                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <span class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-sm">📝</span>
                                                Lainnya
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Alasan Mengajukan Dispen Nikah</label>
                                                    <textarea name="alasan" x-model="alasan" @input="emptyFields['alasan'] = null" rows="3" maxlength="1000" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['alasan']}" placeholder="Jelaskan alasan pengajuan..."></textarea>
                                                    <div class="flex justify-between items-center mt-1">
                                                        <p x-show="formSubmitted && emptyFields['alasan']" class="text-xs text-red-500 validation-warning" x-text="emptyFields['alasan']"></p>
                                                        <p class="text-xs text-slate-400" :class="{'ml-auto': !(formSubmitted && emptyFields['alasan'])}"><span x-text="alasan.length"></span>/1000 karakter</p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Nomor WhatsApp</label>
                                                    <input type="text" name="whatsapp" x-model="whatsapp" @input="whatsapp = sanitizePhone($event.target.value); emptyFields['whatsapp'] = null" maxlength="15" inputmode="numeric" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['whatsapp']}" placeholder="08xxxxxxxxxx">
                                                    <p x-show="whatsapp && !/^(08|628)[0-9]{8,13}$/.test(whatsapp)" class="text-xs text-red-500 mt-1">Format: 08xxxxxxxxxx atau 628xxxxxxxxxx</p>
                                                    <p x-show="formSubmitted && emptyFields['whatsapp'] && (!whatsapp || /^(08|628)[0-9]{8,13}$/.test(whatsapp))" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['whatsapp']"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Upload Berkas -->
                                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-sm">📂</span>
                                                Berkas Persyaratan (Wajib Upload)
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @foreach([
                                                        'ktp_istri' => '1. KTP Calon Istri',
                                                        'ktp_suami' => '2. KTP Calon Suami',
                                                        'kk_istri' => '3. KK Calon Istri',
                                                        'kk_suami' => '4. KK Calon Suami',
                                                        'pas_foto' => '5. Pas Foto Latar Biru',
                                                        'n1_istri' => '6. N1 (Desa Calon Istri)',
                                                        'n1_suami' => '7. N1 (Desa/Kecamatan Calon Suami - Jika Beda)',
                                                        'n2' => '8. N2 Permohonan Kehendak Nikah',
                                                        'n4' => '9. N4 Persetujuan Pengantin',
                                                        'n10' => '10. N10 Rekomendasi KUA'
                                                    ] as $key => $label)
                                                                                                                                <div :class="isRejected('Berkas ' + '{{ strtoupper(str_replace('_', ' ', $key)) }}') ? 'bg-red-50 border-red-300 ring-2 ring-red-100 p-3 rounded-xl border' : ''">
                                                                                                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase" :class="isRejected('Berkas ' + '{{ strtoupper(str_replace('_', ' ', $key)) }}') ? 'text-red-700' : ''">{{ $label }}</label>
                                                                                                                                    <div x-show="isRejected('Berkas ' + '{{ strtoupper(str_replace('_', ' ', $key)) }}')" class="text-xs font-bold text-red-600 mb-2 flex items-center gap-1 bg-red-100/50 w-fit px-2 py-1 rounded">
                                                                                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Perbaiki Berkas Ini
                                                                                                                                    </div>
                                                                                                                                    <input type="file" name="files[{{ $key }}]" @change="handleFileUpload($event, '{{ $key }}')" accept=".pdf,image/*" 
                                                                                                                                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-bedas-50 file:text-bedas-700 hover:file:bg-bedas-100"
                                                                                                                                        :required="selectedLayanan === 'Dispen Nikah' && !isEdit && '{{ $key }}' !== 'n1_suami' && isDraftValue == 0"> 

                                                                                                                        <!-- Existing File Indicator -->
                                                                                                                        <template x-if="isEdit && existingFiles['{{ $key }}']">
                                                                                                                            <div class="mt-2 flex items-center gap-2 text-xs text-bedas-700 bg-bedas-50 px-3 py-1.5 rounded-lg border border-bedas-100 w-fit">
                                                                                                                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                                                                                <span class="truncate max-w-[200px]" x-text="'Terupload: ' + existingFiles['{{ $key }}'].split('/').pop()"></span>
                                                                                                                                <button type="button" @click="viewFile(existingFiles['{{ $key }}'])" class="text-bedas-600 hover:text-bedas-800 underline ml-1 font-bold">Lihat</button>
                                                                                                                            </div>
                                                                                                                        </template> 
                                                                                                                                </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Conditional Input for Izin Keramaian -->
                                    <div x-show="selectedLayanan == 'Izin Keramaian'" class="space-y-8 mb-8">
                                        
                                        <!-- Data Pemohon -->
                                        <div class="p-6 rounded-2xl border" :class="isRejected('Data Pemohon') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-slate-50 border-slate-100'">
                                            <div x-show="isRejected('Data Pemohon')" class="text-xs font-bold text-red-600 mb-3 flex items-center gap-1 bg-red-100/50 w-fit px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                Bagian ini perlu diperbaiki
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm">👤</span>
                                                Data Pemohon
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Nama Lengkap</label>
                                                    <input type="text" name="pemohon[nama]" x-model="pemohon.nama" @input="pemohon.nama = sanitizeNama($event.target.value); emptyFields['pemohon.nama'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['pemohon.nama']}" placeholder="Sesuai KTP">
                                                    <p class="text-xs mt-1" :class="formSubmitted && emptyFields['pemohon.nama'] ? 'text-red-500 validation-warning' : 'text-slate-400'" x-text="formSubmitted && emptyFields['pemohon.nama'] ? emptyFields['pemohon.nama'] : 'Hanya huruf, spasi, titik, dan apostrof'"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">NIK</label>
                                                    <input type="text" name="pemohon[nik]" x-model="pemohon.nik" @input="pemohon.nik = sanitizeNik($event.target.value); validateNik(pemohon.nik, 'pemohon'); emptyFields['pemohon.nik'] = null" maxlength="16" minlength="16" inputmode="numeric" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm font-mono tracking-wider" :class="{'border-red-500 ring-1 ring-red-500': (formSubmitted && emptyFields['pemohon.nik']) || nikErrors['pemohon'], 'border-emerald-500 ring-1 ring-emerald-500': pemohon.nik && pemohon.nik.length === 16 && !nikErrors['pemohon']}" placeholder="Contoh: 3204XXXXDDMMYYXXXX">
                                                    <!-- Progress bar -->
                                                    <div x-show="pemohon.nik && pemohon.nik.length > 0 && pemohon.nik.length < 16" class="mt-1">
                                                        <div class="w-full bg-slate-200 rounded-full h-1">
                                                            <div class="bg-bedas-500 h-1 rounded-full transition-all" :style="'width:' + (pemohon.nik.length / 16 * 100) + '%'"></div>
                                                        </div>
                                                        <p class="text-[10px] text-slate-400 mt-0.5"><span x-text="pemohon.nik.length"></span>/16 digit</p>
                                                    </div>
                                                    <!-- Format breakdown when 16 digits -->
                                                    <div x-show="pemohon.nik && pemohon.nik.length === 16 && !nikErrors['pemohon']" class="mt-1 flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                                        <p class="text-[10px] text-emerald-600 font-medium">NIK Valid — <span class="text-slate-400" x-text="'Wilayah: ' + pemohon.nik.substring(0,2) + '.' + pemohon.nik.substring(2,4) + '.' + pemohon.nik.substring(4,6) + ' | Lahir: ' + nikBirthDate(pemohon.nik) + ' | Urut: ' + pemohon.nik.substring(12,16)"></span></p>
                                                    </div>
                                                    <!-- Error messages -->
                                                    <p x-show="nikErrors['pemohon']" class="text-xs text-red-500 mt-1 flex items-center gap-1"><svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> <span x-text="nikErrors['pemohon']"></span></p>
                                                    <p x-show="formSubmitted && emptyFields['pemohon.nik'] && !nikErrors['pemohon']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['pemohon.nik']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Tempat, Tanggal Lahir</label>
                                                    <input type="text" name="pemohon[ttl]" x-model="pemohon.ttl" @input="pemohon.ttl = sanitizeTtl($event.target.value); emptyFields['pemohon.ttl'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['pemohon.ttl']}" placeholder="Contoh: Bandung, 12 Januari 1990">
                                                    <p x-show="formSubmitted && emptyFields['pemohon.ttl']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['pemohon.ttl']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Jenis Kelamin</label>
                                                    <select name="pemohon[gender]" x-model="pemohon.gender" @change="emptyFields['pemohon.gender'] = null" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['pemohon.gender']}">
                                                        <option value="">Pilih Jenis Kelamin</option>
                                                        <option value="Laki-laki">Laki-laki</option>
                                                        <option value="Perempuan">Perempuan</option>
                                                    </select>
                                                    <p x-show="formSubmitted && emptyFields['pemohon.gender']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['pemohon.gender']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Pekerjaan</label>
                                                    <input type="text" name="pemohon[pekerjaan]" x-model="pemohon.pekerjaan" @input="pemohon.pekerjaan = sanitizePekerjaan($event.target.value); emptyFields['pemohon.pekerjaan'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['pemohon.pekerjaan']}" placeholder="Pekerjaan saat ini">
                                                    <p x-show="formSubmitted && emptyFields['pemohon.pekerjaan']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['pemohon.pekerjaan']"></p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Alamat</label>
                                                    <textarea name="pemohon[alamat]" x-model="pemohon.alamat" @input="emptyFields['pemohon.alamat'] = null" rows="2" maxlength="500" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['pemohon.alamat']}" placeholder="Alamat Lengkap"></textarea>
                                                    <div class="flex justify-between items-center mt-1">
                                                        <p x-show="formSubmitted && emptyFields['pemohon.alamat']" class="text-xs text-red-500 validation-warning" x-text="emptyFields['pemohon.alamat']"></p>
                                                        <p class="text-xs text-slate-400" :class="{'ml-auto': !(formSubmitted && emptyFields['pemohon.alamat'])}"><span x-text="pemohon.alamat.length"></span>/500 karakter</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Detail Keramaian -->
                                        <div class="p-6 rounded-2xl border" :class="isRejected('Maksud Keramaian') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-slate-50 border-slate-100'">
                                            <div x-show="isRejected('Maksud Keramaian')" class="text-xs font-bold text-red-600 mb-3 flex items-center gap-1 bg-red-100/50 w-fit px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                Bagian ini perlu diperbaiki
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <span class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-sm">🎉</span>
                                                Maksud Mengadakan Keramaian
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Hari / Tanggal</label>
                                                    <input type="date" name="keramaian[tanggal]" x-model="keramaian.tanggal" @input="emptyFields['keramaian.tanggal'] = null" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['keramaian.tanggal']}">
                                                    <p x-show="formSubmitted && emptyFields['keramaian.tanggal']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['keramaian.tanggal']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Acara</label>
                                                    <input type="text" name="keramaian[acara]" x-model="keramaian.acara" @input="keramaian.acara = sanitizeAcara($event.target.value); emptyFields['keramaian.acara'] = null" maxlength="200" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['keramaian.acara']}" placeholder="Contoh: Pernikahan / Khitanan">
                                                    <p x-show="formSubmitted && emptyFields['keramaian.acara']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['keramaian.acara']"></p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Lokasi</label>
                                                    <input type="text" name="keramaian[lokasi]" x-model="keramaian.lokasi" @input="emptyFields['keramaian.lokasi'] = null" maxlength="500" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['keramaian.lokasi']}" placeholder="Lokasi Lengkap Acara">
                                                    <p x-show="formSubmitted && emptyFields['keramaian.lokasi']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['keramaian.lokasi']"></p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Hiburan</label>
                                                    <input type="text" name="keramaian[hiburan]" x-model="keramaian.hiburan" @input="keramaian.hiburan = sanitizeAcara($event.target.value); emptyFields['keramaian.hiburan'] = null" maxlength="200" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['keramaian.hiburan']}" placeholder="Contoh: Orgen Tunggal / Wayang Golek">
                                                    <p x-show="formSubmitted && emptyFields['keramaian.hiburan']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['keramaian.hiburan']"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Upload Berkas Izin Keramaian -->
                                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <span class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-sm">📂</span>
                                                Berkas Persyaratan
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @foreach([
                                                        'ktp' => '1. KTP Pemohon',
                                                        'proposal_acara' => '2. Proposal Acara'
                                                    ] as $key => $label)
                                                                                                        <div :class="isRejected('Berkas ' + '{{ strtoupper(str_replace('_', ' ', $key)) }}') ? 'bg-red-50 border-red-300 ring-2 ring-red-100 p-3 rounded-xl border' : ''">
                                                                                                            <label class="block text-slate-700 text-xs font-bold mb-1 uppercase" :class="isRejected('Berkas ' + '{{ strtoupper(str_replace('_', ' ', $key)) }}') ? 'text-red-700' : ''">{{ $label }}</label>
                                                                                                            <div x-show="isRejected('Berkas ' + '{{ strtoupper(str_replace('_', ' ', $key)) }}')" class="text-xs font-bold text-red-600 mb-2 flex items-center gap-1 bg-red-100/50 w-fit px-2 py-1 rounded">
                                                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Perbaiki Berkas Ini
                                                                                                            </div>
                                                                                                            <input type="file" name="files[{{ $key }}]" @change="handleFileUpload($event, '{{ $key }}')" accept=".pdf,image/*" 
                                                                                                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-bedas-50 file:text-bedas-700 hover:file:bg-bedas-100"
                                                                                                                :required="selectedLayanan === 'Izin Keramaian' && !isEdit && isDraftValue == 0"> 

                                                                                                            <template x-if="isEdit && existingFiles['{{ $key }}']">
                                                                                                                <div class="mt-2 flex items-center gap-2 text-xs text-bedas-700 bg-bedas-50 px-3 py-1.5 rounded-lg border border-bedas-100 w-fit">
                                                                                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                                                                    <span class="truncate max-w-[200px]" x-text="'Terupload: ' + existingFiles['{{ $key }}'].split('/').pop()"></span>
                                                                                                                    <button type="button" @click="viewFile(existingFiles['{{ $key }}'])" class="text-bedas-600 hover:text-bedas-800 underline ml-1 font-bold">Lihat</button>
                                                                                                                </div>
                                                                                                            </template> 
                                                                                                        </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Conditional Input for Rekomendasi Bantuan -->
                                    <div x-show="selectedLayanan == 'Rekomendasi Bantuan'" class="space-y-8 mb-8">
                                        <!-- Data Rekomendasi -->
                                        <div class="p-6 rounded-2xl border" :class="isRejected('Data Rekomendasi Bantuan') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-slate-50 border-slate-100'">
                                            <div x-show="isRejected('Data Rekomendasi Bantuan')" class="text-xs font-bold text-red-600 mb-3 flex items-center gap-1 bg-red-100/50 w-fit px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                Bagian ini perlu diperbaiki
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <span class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-sm">📋</span>
                                                Data Rekomendasi Bantuan
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Jenis Kelompok</label>
                                                    <input type="text" name="rekomendasi[jenis_kelompok]" x-model="rekomendasi.jenis_kelompok" @input="rekomendasi.jenis_kelompok = sanitizeJenisKelompok($event.target.value); emptyFields['rekomendasi.jenis_kelompok'] = null" maxlength="200" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['rekomendasi.jenis_kelompok']}" placeholder="Contoh: Kelompok Tani">
                                                    <p x-show="formSubmitted && emptyFields['rekomendasi.jenis_kelompok']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['rekomendasi.jenis_kelompok']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Nama Kelompok</label>
                                                    <input type="text" name="rekomendasi[nama_kelompok]" x-model="rekomendasi.nama_kelompok" @input="rekomendasi.nama_kelompok = sanitizeNamaKelompok($event.target.value); emptyFields['rekomendasi.nama_kelompok'] = null" maxlength="200" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['rekomendasi.nama_kelompok']}" placeholder="Nama Kelompok">
                                                    <p x-show="formSubmitted && emptyFields['rekomendasi.nama_kelompok']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['rekomendasi.nama_kelompok']"></p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Alamat Lengkap</label>
                                                    <textarea name="rekomendasi[alamat]" x-model="rekomendasi.alamat" @input="emptyFields['rekomendasi.alamat'] = null" rows="2" maxlength="500" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['rekomendasi.alamat']}" placeholder="Alamat Kelompok"></textarea>
                                                    <div class="flex justify-between items-center mt-1">
                                                        <p x-show="formSubmitted && emptyFields['rekomendasi.alamat']" class="text-xs text-red-500 validation-warning" x-text="emptyFields['rekomendasi.alamat']"></p>
                                                        <p class="text-xs text-slate-400" :class="{'ml-auto': !(formSubmitted && emptyFields['rekomendasi.alamat'])}"><span x-text="rekomendasi.alamat.length"></span>/500 karakter</p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Perihal</label>
                                                    <input type="text" name="rekomendasi[perihal]" x-model="rekomendasi.perihal" @input="emptyFields['rekomendasi.perihal'] = null" maxlength="500" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['rekomendasi.perihal']}" placeholder="Perihal Bantuan">
                                                    <p x-show="formSubmitted && emptyFields['rekomendasi.perihal']" class="text-xs text-red-500 mt-1 validation-warning" x-text="emptyFields['rekomendasi.perihal']"></p>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-700 text-xs font-bold mb-1 uppercase">Nama Desa</label>
                                                    <input type="text" name="rekomendasi[nama_desa]" x-model="rekomendasi.nama_desa" @input="rekomendasi.nama_desa = sanitizeNamaDesa($event.target.value); emptyFields['rekomendasi.nama_desa'] = null" maxlength="100" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" :class="{'border-red-500 ring-1 ring-red-500': formSubmitted && emptyFields['rekomendasi.nama_desa']}" placeholder="Nama Desa">
                                                    <p class="text-xs mt-1" :class="formSubmitted && emptyFields['rekomendasi.nama_desa'] ? 'text-red-500 validation-warning' : 'text-slate-400'" x-text="formSubmitted && emptyFields['rekomendasi.nama_desa'] ? emptyFields['rekomendasi.nama_desa'] : 'Hanya huruf dan spasi'"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Upload Berkas Rekomendasi Bantuan -->
                                        <div class="p-6 rounded-2xl border" :class="isRejected('Berkas Persyaratan') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-slate-50 border-slate-100'">
                                            <div x-show="isRejected('Berkas Persyaratan')" class="text-xs font-bold text-red-600 mb-3 flex items-center gap-1 bg-red-100/50 w-fit px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                Bagian ini perlu diperbaiki
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                                                <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-sm">📂</span>
                                                Berkas Persyaratan
                                            </h4>
                                            <div class="grid grid-cols-1 gap-4">
                                                @foreach([
                                                        'proposal' => '1. Proposal Bantuan'
                                                    ] as $key => $label)
                                                        <div :class="isRejected('Berkas ' + '{{ strtoupper(str_replace('_', ' ', $key)) }}') ? 'bg-red-50 border-red-300 ring-2 ring-red-100 p-3 rounded-xl border' : ''">
                                                            <label class="block text-slate-700 text-xs font-bold mb-1 uppercase" :class="isRejected('Berkas ' + '{{ strtoupper(str_replace('_', ' ', $key)) }}') ? 'text-red-700' : ''">{{ $label }}</label>
                                                            <div x-show="isRejected('Berkas ' + '{{ strtoupper(str_replace('_', ' ', $key)) }}')" class="text-xs font-bold text-red-600 mb-2 flex items-center gap-1 bg-red-100/50 w-fit px-2 py-1 rounded">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Perbaiki Berkas Ini
                                                            </div>
                                                            <input type="file" name="files[{{ $key }}]" @change="handleFileUpload($event, '{{ $key }}')" accept=".pdf,image/*" 
                                                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-bedas-50 file:text-bedas-700 hover:file:bg-bedas-100"
                                                                :required="selectedLayanan === 'Rekomendasi Bantuan' && !isEdit && isDraftValue == 0"> 

                                                            <template x-if="isEdit && existingFiles['{{ $key }}']">
                                                                <div class="mt-2 flex items-center gap-2 text-xs text-bedas-700 bg-bedas-50 px-3 py-1.5 rounded-lg border border-bedas-100 w-fit">
                                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                                    <span class="truncate max-w-[200px]" x-text="'Terupload: ' + existingFiles['{{ $key }}'].split('/').pop()"></span>
                                                                    <button type="button" @click="viewFile(existingFiles['{{ $key }}'])" class="text-bedas-600 hover:text-bedas-800 underline ml-1 font-bold">Lihat</button>
                                                                </div>
                                                            </template> 
                                                        </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Single File Upload for Other Services -->
                                    <div x-show="selectedLayanan != 'Dispen Nikah' && selectedLayanan != 'Izin Keramaian' && selectedLayanan != 'Rekomendasi Bantuan'" class="mb-6">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="block text-slate-700 text-sm font-semibold mb-0" :class="isRejected('Berkas MAIN FILE') ? 'text-red-700' : ''">Unggah Berkas Persyaratan (PDF/IMG)</label>
                                            <div x-show="isRejected('Berkas MAIN FILE')" class="text-xs font-bold text-red-600 flex items-center gap-1 bg-red-100/50 w-fit px-3 py-1 rounded-lg">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Perbaiki Berkas Ini
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-center w-full">
                                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full min-h-[128px] border-2 border-dashed rounded-xl cursor-pointer transition-colors relative overflow-hidden" :class="isRejected('Berkas MAIN FILE') ? 'border-red-400 bg-red-50 hover:bg-red-100 ring-4 ring-red-500/10' : 'border-slate-300 bg-slate-50 hover:bg-slate-100'">
                                                <!-- Initial State -->
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="!fileName">
                                                    <svg class="w-8 h-8 mb-3 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                    </svg>
                                                    <p class="mb-2 text-sm text-slate-500"><span class="font-semibold">Klik untuk upload</span></p>
                                                </div>
                                                <!-- Selected State -->
                                                <div class="flex flex-col items-center justify-center w-full h-full p-4" x-show="fileName" style="display: none;">
                                                    <template x-if="filePreview">
                                                        <img :src="filePreview" class="max-h-48 rounded-lg mb-3 shadow-sm object-contain" alt="Preview">
                                                    </template>
                                                    <template x-if="!filePreview">
                                                        <div class="w-16 h-16 bg-red-50 text-red-500 rounded-lg flex items-center justify-center text-2xl mb-3">📄</div>
                                                    </template>
                                                    <p class="text-sm font-medium text-slate-700 truncate max-w-[80%]" x-text="fileName"></p>
                                                </div>
                                                <input id="dropzone-file" name="berkas" type="file" class="hidden" @change="handleFileUpload($event, 'main')" accept=".pdf,image/*" :required="selectedLayanan != 'Dispen Nikah' && selectedLayanan != 'Izin Keramaian' && selectedLayanan != 'Rekomendasi Bantuan' && !isEdit && isDraftValue == 0" />
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-3">
                                        <input type="hidden" name="is_draft" x-model="isDraftValue">
                                        <button type="button" @click="resetForm()"
                                            class="bg-white border border-slate-300 text-slate-700 font-medium py-2 px-5 rounded-lg hover:bg-slate-50 transition-colors">
                                            Batal
                                        </button>
                                        <button type="submit" @click="isDraftValue = 1" formnovalidate
                                            class="bg-slate-100 text-slate-700 font-bold py-2 px-6 rounded-lg hover:bg-slate-200 transition-all">
                                            Simpan sebagai Draft
                                        </button>
                                        <button type="submit" @click="isDraftValue = 0"
                                            class="bg-bedas-600 hover:bg-bedas-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg shadow-bedas-200 transition-all transform hover:-translate-y-0.5">
                                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Kirim Permohonan'"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                
                    <!-- History Tab -->
                    <div x-show="activeTab === 'history'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                        
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-100">
                                <h3 class="text-lg font-bold text-slate-800">Riwayat Permohonan Saya</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full leading-normal">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">No Antrean</th>
                                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Layanan</th>
                                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        @foreach($permohonans as $p)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-no-wrap text-sm font-mono font-medium text-slate-700">{{ $p->no_antrean }}</td>
                                                <td class="px-6 py-4 whitespace-no-wrap text-sm text-slate-600">{{ $p->jenis_layanan }}</td>
                                                <td class="px-6 py-4 whitespace-no-wrap text-sm text-slate-500">{{ $p->created_at->format('d M Y') }}</td>
                                                <td class="px-6 py-4 whitespace-no-wrap text-sm">
                                                    @if($p->status == 'pending')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Menunggu Validasi</span>
                                                    @elseif($p->status == 'draft')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">Draft</span>
                                                    @elseif($p->status == 'ditolak')
                                                        <div class="flex flex-col">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">Ditolak</span>
                                                        </div>
                                                    @elseif($p->status == 'menunggu_camat')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Menunggu TTD Camat</span>
                                                    @elseif($p->status == 'ditandatangani')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Ditandatangani - Menunggu Penomoran</span>
                                                    @elseif($p->status == 'selesai')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Selesai</span>
                                                    @endif

                                                    @if($p->status == 'pending' && $p->queue_position)
                                                        <div class="mt-1 text-xs text-slate-500 font-medium">Antrean ke-<span class="text-bedas-600 font-bold">{{ $p->queue_position }}</span></div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-no-wrap text-sm flex items-center gap-2">
                                                    <!-- Detail Button -->
                                                    <div class="flex items-center gap-3">
                                                    <button @click="showDetailModal = true; selectedPermohonan = {{ $p }}" class="text-blue-500 hover:text-blue-700 transition-colors" title="Lihat Detail">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    </button>

                                                    <!-- Print Button for Completed Items -->
                                                    @if($p->status === 'selesai')
                                                        <button @click="viewPrint('{{ route('masyarakat.print', $p->id) }}')" class="text-bedas-600 hover:text-bedas-800" title="Cetak Surat">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                        </button>
                                                    @endif

                                                    @if($p->status === 'pending' || $p->status === 'ditolak' || $p->status === 'draft')
                                                        <div class="flex gap-2">
                                                        <button @click="editPermohonan({{ $p }})"
                                                            class="text-yellow-500 hover:text-yellow-700 transition-colors" title="Ubah">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        </button>
                                                        <!-- Delete Button -->
                                                        <button @click="showDeleteModal = true; deleteId = {{ $p->id }}"
                                                            class="text-red-500 hover:text-red-700 transition-colors" title="Hapus">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if($permohonans->isEmpty())
                                            <tr>
                                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada riwayat permohonan.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <!-- Draft Tab -->
                    <div x-show="activeTab === 'draft'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                        
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                            <div class="p-6 border-b border-slate-100">
                                <h3 class="text-lg font-bold text-slate-800">Draft Permohonan Saya</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full leading-normal">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Layanan</th>
                                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Terakhir Diperbarui</th>
                                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-100">
                                        @foreach($drafts as $d)
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-no-wrap text-sm text-slate-600">{{ $d->jenis_layanan }}</td>
                                                <td class="px-6 py-4 whitespace-no-wrap text-sm text-slate-500">{{ $d->updated_at->format('d M Y, H:i') }}</td>
                                                <td class="px-6 py-4 whitespace-no-wrap text-sm">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">Draft</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-no-wrap text-sm flex items-center gap-2">
                                                    <div class="flex items-center gap-3">
                                                        <button @click="showDetailModal = true; selectedPermohonan = {{ $d }}" class="text-blue-500 hover:text-blue-700 transition-colors" title="Lihat Detail">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        </button>
                                                        <button @click="editPermohonan({{ $d }})"
                                                            class="text-yellow-500 hover:text-yellow-700 transition-colors" title="Lanjutkan Isi Form">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        </button>
                                                        <button @click="showDeleteModal = true; deleteId = {{ $d->id }}"
                                                            class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Draft">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if($drafts->isEmpty())
                                            <tr>
                                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Belum ada draft permohonan.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                </div>
            </div>

        </div>

        <!-- Detail Modal -->
        <div x-show="showDetailModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDetailModal" class="fixed inset-0 transition-opacity" aria-hidden="true"
                    @click="showDetailModal = false">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showDetailModal"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <h3 class="text-xl font-bold text-slate-900 mb-4">Detail Permohonan</h3>
                        <template x-if="selectedPermohonan">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide">Nomor Antrean</label>
                                    <p class="text-lg font-mono font-bold text-slate-800" x-text="selectedPermohonan.no_antrean"></p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide">Jenis Layanan</label>
                                    <p class="text-slate-800" x-text="selectedPermohonan.jenis_layanan"></p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide">Status Terakhir</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-orange-100 text-orange-800': selectedPermohonan.status == 'pending',
                                            'bg-slate-100 text-slate-800': selectedPermohonan.status == 'draft',
                                            'bg-red-100 text-red-800': selectedPermohonan.status == 'ditolak',
                                            'bg-blue-100 text-blue-800': selectedPermohonan.status == 'menunggu_camat',
                                            'bg-emerald-100 text-emerald-800': selectedPermohonan.status == 'disetujui'
                                        }" x-text="selectedPermohonan.status.replace('_', ' ').toUpperCase()"></span>
                                </div>
                                <template x-if="selectedPermohonan.status === 'ditolak'">
                                    <div class="bg-red-50 p-4 rounded-xl border border-red-200 space-y-3">
                                        <div class="flex items-start gap-3">
                                            <div class="bg-red-100 text-red-600 p-2 rounded-lg shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-red-800 font-bold text-sm">Permohonan Dikembalikan</h4>
                                                <p class="text-red-600 text-xs mt-1">Terdapat data atau berkas yang tidak sesuai. Silakan buat permohonan baru dengan memperbaiki bagian yang salah.</p>
                                            </div>
                                        </div>

                                        <template x-if="parseItems(selectedPermohonan.invalid_items).length > 0">
                                            <div class="pl-11">
                                                <p class="text-xs font-semibold text-red-800 mb-2 uppercase tracking-wide">Item yang tidak sesuai:</p>
                                                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                                    <template x-for="item in parseItems(selectedPermohonan.invalid_items)">
                                                        <li x-text="item"></li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </template>

                                        <template x-if="parseItems(selectedPermohonan.invalid_items).length === 0">
                                            <div class="pl-11">
                                                <p class="text-xs text-red-600">Mohon periksa permohonan Anda kembali sesuai catatan dari petugas di bawah.</p>
                                            </div>
                                        </template>

                                        <template x-if="selectedPermohonan.keterangan">
                                            <div class="pl-11 pt-2 border-t border-red-100 mt-2">
                                                <p class="text-xs font-semibold text-red-800 mb-1 uppercase tracking-wide">Catatan Tambahan:</p>
                                                <p class="text-sm text-red-700 italic" x-text="selectedPermohonan.keterangan"></p>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Metadata Display for Dispen Nikah -->
                                <template x-if="selectedPermohonan.jenis_layanan === 'Dispen Nikah' && selectedPermohonan.metadata">
                                    <div class="space-y-4 mt-4">
                                        <!-- Data Pasangan -->
                                        <div class="p-4 rounded-xl border" :class="isDetailRejected('Data Calon Suami') || isDetailRejected('Data Calon Istri') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-slate-50 border-slate-100'">
                                            <div x-show="isDetailRejected('Data Calon Suami') || isDetailRejected('Data Calon Istri')" class="text-xs font-bold text-red-600 mb-2 flex items-center gap-1 bg-red-100/50 w-fit px-2 py-1 rounded">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Data Pasangan Tidak Sesuai
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-3 text-sm flex items-center gap-2">
                                                <span>❤️</span> Data Pasangan
                                            </h4>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <p class="text-xs text-slate-500 font-semibold mb-1">CALON SUAMI</p>
                                                    <div class="space-y-1">
                                                        <p><span class="text-slate-400 w-20 inline-block">Nama:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.suami?.nama"></span></p>
                                                        <p><span class="text-slate-400 w-20 inline-block">NIK:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.suami?.nik"></span></p>
                                                        <p><span class="text-slate-400 w-20 inline-block">TTL:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.suami?.ttl"></span></p>
                                                        <p><span class="text-slate-400 w-20 inline-block">Agama:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.suami?.agama"></span></p>
                                                        <p><span class="text-slate-400 w-20 inline-block">Pekerjaan:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.suami?.pekerjaan"></span></p>
                                                        <p><span class="text-slate-400 w-20 inline-block">Alamat:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.suami?.alamat"></span></p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-slate-500 font-semibold mb-1">CALON ISTRI</p>
                                                    <div class="space-y-1">
                                                        <p><span class="text-slate-400 w-20 inline-block">Nama:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.istri?.nama"></span></p>
                                                        <p><span class="text-slate-400 w-20 inline-block">NIK:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.istri?.nik"></span></p>
                                                        <p><span class="text-slate-400 w-20 inline-block">TTL:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.istri?.ttl"></span></p>
                                                        <p><span class="text-slate-400 w-20 inline-block">Agama:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.istri?.agama"></span></p>
                                                        <p><span class="text-slate-400 w-20 inline-block">Pekerjaan:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.istri?.pekerjaan"></span></p>
                                                        <p><span class="text-slate-400 w-20 inline-block">Alamat:</span> <span class="text-slate-700" x-text="selectedPermohonan.metadata.istri?.alamat"></span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Rencana Pernikahan -->
                                        <div class="p-4 rounded-xl border" :class="isDetailRejected('Rencana Pernikahan') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-purple-50 border-purple-100'">
                                            <div x-show="isDetailRejected('Rencana Pernikahan')" class="text-xs font-bold text-red-600 mb-2 flex items-center gap-1 bg-red-100/50 w-fit px-2 py-1 rounded">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Rencana Pernikahan Tidak Sesuai
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-3 text-sm flex items-center gap-2">
                                                <span>📅</span> Rencana Pernikahan
                                            </h4>
                                            <div class="grid grid-cols-2 gap-4 text-sm">
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
                                        <div class="grid grid-cols-1 gap-4">
                                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-1">Alasan Pengajuan</h4>
                                                <p class="text-sm text-slate-700" x-text="selectedPermohonan.metadata.alasan || '-'"></p>
                                            </div>
                                            <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-1">WhatsApp</h4>
                                                <p class="text-sm text-slate-700 font-mono" x-text="selectedPermohonan.metadata.whatsapp || '-'"></p>
                                            </div>
                                        </div>

                                        <!-- File List (Dispen Nikah) -->
                                        <template x-if="selectedPermohonan.metadata.files">
                                            <div class="mt-4">
                                                <h4 class="font-bold text-slate-800 mb-3 text-sm">Berkas Lampiran</h4>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    <template x-for="(path, key) in selectedPermohonan.metadata.files" :key="key">
                                                        <button type="button" @click="viewFile(path)" class="flex items-center p-2 rounded-lg transition-all group text-left w-full border" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'bg-red-50 border-red-300 ring-2 ring-red-100 hover:border-red-400' : 'bg-white border-slate-200 hover:border-bedas-300 hover:shadow-sm'">
                                                            <div class="w-8 h-8 rounded flex items-center justify-center mr-3" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'bg-red-100 text-red-500' : 'bg-slate-100 text-slate-500 group-hover:bg-bedas-50 group-hover:text-bedas-600'">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                            </div>
                                                            <div class="overflow-hidden">
                                                                <p class="text-xs font-bold uppercase" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'text-red-700' : 'text-slate-700'" x-text="key.replace('_', ' ').replace('ktp', 'KTP').replace('kk', 'KK')"></p>
                                                                <p class="text-[10px] truncate" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'text-red-500' : 'text-slate-400'" x-text="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'Berkas Tidak Sesuai' : 'Klik untuk lihat'"></p>
                                                            </div>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Metadata Display for Izin Keramaian -->
                                <template x-if="selectedPermohonan.jenis_layanan === 'Izin Keramaian' && selectedPermohonan.metadata">
                                    <div class="space-y-4 mt-4">
                                        <!-- Data Pemohon -->
                                        <div class="p-4 rounded-xl border" :class="isDetailRejected('Data Pemohon') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-slate-50 border-slate-100'">
                                            <div x-show="isDetailRejected('Data Pemohon')" class="text-xs font-bold text-red-600 mb-2 flex items-center gap-1 bg-red-100/50 w-fit px-2 py-1 rounded">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Data Pemohon Tidak Sesuai
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-3 text-sm flex items-center gap-2">
                                                <span>👤</span> Data Pemohon
                                            </h4>
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
                                        <div class="p-4 rounded-xl border" :class="isDetailRejected('Maksud Keramaian') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-orange-50 border-orange-100'">
                                            <div x-show="isDetailRejected('Maksud Keramaian')" class="text-xs font-bold text-red-600 mb-2 flex items-center gap-1 bg-red-100/50 w-fit px-2 py-1 rounded">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Maksud Keramaian Tidak Sesuai
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-3 text-sm flex items-center gap-2">
                                                <span>🎉</span> Maksud Keramaian
                                            </h4>
                                            <div class="space-y-2 text-sm">
                                                <p><span class="text-slate-500 w-32 inline-block">Hari / Tanggal:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.keramaian?.tanggal"></span></p>
                                                <p><span class="text-slate-500 w-32 inline-block">Acara:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.keramaian?.acara"></span></p>
                                                <p><span class="text-slate-500 w-32 inline-block">Lokasi:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.keramaian?.lokasi"></span></p>
                                                <p><span class="text-slate-500 w-32 inline-block">Hiburan:</span> <span class="font-medium text-slate-800" x-text="selectedPermohonan.metadata.keramaian?.hiburan"></span></p>
                                            </div>
                                        </div>

                                        <!-- File List (Izin Keramaian) -->
                                        <template x-if="selectedPermohonan.metadata.files">
                                            <div class="mt-4">
                                                <h4 class="font-bold text-slate-800 mb-3 text-sm">Berkas Lampiran</h4>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    <template x-for="(path, key) in selectedPermohonan.metadata.files" :key="key">
                                                        <button type="button" @click="viewFile(path)" class="flex items-center p-2 rounded-lg transition-all group text-left w-full border" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'bg-red-50 border-red-300 ring-2 ring-red-100 hover:border-red-400' : 'bg-white border-slate-200 hover:border-bedas-300 hover:shadow-sm'">
                                                            <div class="w-8 h-8 rounded flex items-center justify-center mr-3" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'bg-red-100 text-red-500' : 'bg-slate-100 text-slate-500 group-hover:bg-bedas-50 group-hover:text-bedas-600'">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                            </div>
                                                            <div class="overflow-hidden">
                                                                <p class="text-xs font-bold uppercase" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'text-red-700' : 'text-slate-700'" x-text="key.replace('_', ' ').replace('ktp', 'KTP').replace('kk', 'KK')"></p>
                                                                <p class="text-[10px] truncate" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'text-red-500' : 'text-slate-400'" x-text="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'Berkas Tidak Sesuai' : 'Klik untuk lihat'"></p>
                                                            </div>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Metadata Display for Rekomendasi Bantuan -->
                                <template x-if="selectedPermohonan.jenis_layanan === 'Rekomendasi Bantuan' && selectedPermohonan.metadata">
                                    <div class="space-y-4 mt-4">
                                        <!-- Data Rekomendasi -->
                                        <div class="p-4 rounded-xl border" :class="isDetailRejected('Data Rekomendasi Bantuan') ? 'bg-red-50 border-red-300 ring-2 ring-red-100' : 'bg-slate-50 border-slate-100'">
                                            <div x-show="isDetailRejected('Data Rekomendasi Bantuan')" class="text-xs font-bold text-red-600 mb-2 flex items-center gap-1 bg-red-100/50 w-fit px-2 py-1 rounded">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg> Data Rekomendasi Tidak Sesuai
                                            </div>
                                            <h4 class="font-bold text-slate-800 mb-3 text-sm flex items-center gap-2">
                                                <span>📋</span> Data Rekomendasi Bantuan
                                            </h4>
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
                                        <template x-if="selectedPermohonan.metadata.files">
                                            <div class="mt-4">
                                                <h4 class="font-bold text-slate-800 mb-3 text-sm">Berkas Lampiran</h4>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    <template x-for="(path, key) in selectedPermohonan.metadata.files" :key="key">
                                                        <button type="button" @click="viewFile(path)" class="flex items-center p-2 rounded-lg transition-all group text-left w-full border" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'bg-red-50 border-red-300 ring-2 ring-red-100 hover:border-red-400' : 'bg-white border-slate-200 hover:border-bedas-300 hover:shadow-sm'">
                                                            <div class="w-8 h-8 rounded flex items-center justify-center mr-3" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'bg-red-100 text-red-500' : 'bg-slate-100 text-slate-500 group-hover:bg-bedas-50 group-hover:text-bedas-600'">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                            </div>
                                                            <div class="overflow-hidden">
                                                                <p class="text-xs font-bold uppercase" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'text-red-700' : 'text-slate-700'" x-text="key.replace('_', ' ')"></p>
                                                                <p class="text-[10px]" :class="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'text-red-500' : 'text-slate-400 truncate'" x-text="isDetailRejected('Berkas ' + key.replace('_', ' ').toUpperCase()) ? 'Berkas Tidak Sesuai' : 'Klik untuk lihat'"></p>
                                                            </div>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse">
                        <button type="button" @click="showDetailModal = false"
                            class="w-full inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors sm:w-auto">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Preview Modal -->
        <div x-show="showPreviewModal" class="fixed z-[60] inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
                <div x-show="showPreviewModal" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showPreviewModal = false">
                    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
                </div>

                <div x-show="showPreviewModal"
                    class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full h-[85vh] flex flex-col">
                    
                    <div class="bg-white px-4 py-3 border-b border-slate-100 flex justify-between items-center shrink-0">
                        <h3 class="text-lg font-bold text-slate-800">Preview Dokumen</h3>
                        <button @click="showPreviewModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 bg-slate-100 p-4 overflow-hidden relative flex items-center justify-center">
                        <template x-if="previewType === 'image'">
                            <img :src="previewUrl" class="max-w-full max-h-full object-contain rounded-lg shadow-sm">
                        </template>
                        <template x-if="previewType === 'pdf'">
                            <iframe :src="previewUrl" class="w-full h-full rounded-lg border border-slate-200"></iframe>
                        </template>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 shrink-0 flex justify-end gap-3">

                        <button type="button" @click="showPreviewModal = false"
                            class="inline-flex justify-center rounded-xl border border-transparent px-4 py-2 bg-bedas-600 text-sm font-semibold text-white hover:bg-bedas-700 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDeleteModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showDeleteModal"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <form x-bind:action="'/masyarakat/permohonan/' + deleteId" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-red-100 text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-center text-slate-900 mb-2">Hapus Permohonan?</h3>
                            <p class="text-center text-slate-500 text-sm">Tindakan ini tidak dapat dibatalkan. Permohonan Anda akan dihapus secara permanen.</p>
                        </div>
                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent px-4 py-2 bg-red-600 text-sm font-semibold text-white hover:bg-red-700 transition-colors sm:w-auto">
                                Hapus
                            </button>
                            <button type="button" @click="showDeleteModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors sm:mt-0 sm:w-auto">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>