<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Kelola Pengguna') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        showAddModal: false,
        showEditModal: false,
        showDeleteModal: false,
        editUser: null,
        deleteUser: null,
        activeTab: 'semua',

        openEdit(user) {
            this.editUser = JSON.parse(JSON.stringify(user));
            this.editUser.password = '';
            this.editUser.password_confirmation = '';
            this.showEditModal = true;
        },

        openDelete(user) {
            this.deleteUser = user;
            this.showDeleteModal = true;
        }
    }">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6 sm:p-8">
                    {{-- Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Daftar Pengguna</h3>
                            <p class="text-slate-500 text-sm">Kelola akun pengguna sistem.</p>
                        </div>
                        <button @click="showAddModal = true"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-bedas-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-bedas-200 hover:bg-bedas-700 transition-all transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Tambah Pengguna
                        </button>
                    </div>

                    {{-- Tabs --}}
                    <div class="flex flex-wrap gap-2 mb-6 border-b border-slate-100 pb-4">
                        <button @click="activeTab = 'semua'" :class="activeTab === 'semua' ? 'bg-slate-800 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide transition-all">
                            Semua <span class="ml-1 opacity-75">({{ $users->count() }})</span>
                        </button>
                        <button @click="activeTab = 'camat'" :class="activeTab === 'camat' ? 'bg-purple-600 text-white shadow-md' : 'bg-purple-50 text-purple-600 hover:bg-purple-100'"
                            class="px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide transition-all">
                            Camat <span class="ml-1 opacity-75">({{ $users->where('role', 'camat')->count() }})</span>
                        </button>
                        <button @click="activeTab = 'petugas'" :class="activeTab === 'petugas' ? 'bg-blue-600 text-white shadow-md' : 'bg-blue-50 text-blue-600 hover:bg-blue-100'"
                            class="px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide transition-all">
                            Petugas <span class="ml-1 opacity-75">({{ $users->where('role', 'petugas')->count() }})</span>
                        </button>
                        <button @click="activeTab = 'masyarakat'" :class="activeTab === 'masyarakat' ? 'bg-emerald-600 text-white shadow-md' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100'"
                            class="px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wide transition-all">
                            Masyarakat <span class="ml-1 opacity-75">({{ $users->where('role', 'masyarakat')->count() }})</span>
                        </button>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengguna</th>
                                    <th class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Kontak</th>
                                    <th class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                                    <th class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Status</th>
                                    <th class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-50">
                                @foreach($users as $user)
                                    <tr class="hover:bg-slate-50/50 transition-colors"
                                        x-show="activeTab === 'semua' || activeTab === '{{ $user->role }}'">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center">
                                                <div class="h-9 w-9 rounded-full flex items-center justify-center font-bold text-xs mr-3 shrink-0
                                                    {{ $user->role === 'camat' ? 'bg-purple-100 text-purple-700' : ($user->role === 'petugas' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $user->name }}</p>
                                                    @if($user->nip)
                                                        <p class="text-[11px] text-slate-400 font-mono">NIP: {{ $user->nip }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 hidden sm:table-cell">
                                            <p class="text-sm text-slate-700 truncate">{{ $user->email }}</p>
                                            @if($user->phone)
                                                <p class="text-xs text-slate-400 font-mono">{{ $user->phone }}</p>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            @if($user->role === 'camat')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-purple-100 text-purple-700">Camat</span>
                                            @elseif($user->role === 'petugas')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-blue-100 text-blue-700">Petugas</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-emerald-100 text-emerald-700">Masyarakat</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 hidden md:table-cell">
                                            @if($user->role === 'camat')
                                                @if($user->is_active)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-green-100 text-green-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-red-100 text-red-600">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-xs text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            @if($user->id !== auth()->id())
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button @click="openEdit({{ $user->toJson() }})"
                                                        class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                    <button @click="openDelete({{ $user->toJson() }})"
                                                        class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-[10px] text-slate-400 font-medium uppercase tracking-wide bg-slate-50 px-2 py-1 rounded-md">Anda</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($users->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl">👤</span>
                            </div>
                            <p class="text-slate-500">Belum ada pengguna.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- ADD USER MODAL --}}
        {{-- ========================================= --}}
        <div x-show="showAddModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity" @click="showAddModal = false">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                    <form method="POST" action="{{ route('petugas.users.store') }}">
                        @csrf
                        <div class="bg-white px-6 pt-6 pb-4">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-bedas-100 text-bedas-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">Tambah Pengguna Baru</h3>
                                    <p class="text-xs text-slate-500">Lengkapi data pengguna di bawah ini.</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" placeholder="Nama lengkap">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" required class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" placeholder="email@contoh.com">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">No. WhatsApp</label>
                                        <input type="text" name="phone" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" placeholder="08xxxxxxxxxx">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">NIP</label>
                                        <input type="text" name="nip" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" placeholder="Khusus Camat">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Role <span class="text-red-500">*</span></label>
                                    <select name="role" required class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm">
                                        <option value="">— Pilih Role —</option>
                                        <option value="camat">Camat</option>
                                        <option value="petugas">Petugas</option>
                                        <option value="masyarakat">Masyarakat</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Password <span class="text-red-500">*</span></label>
                                        <input type="password" name="password" required class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" placeholder="Min. 8 karakter">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Konfirmasi <span class="text-red-500">*</span></label>
                                        <input type="password" name="password_confirmation" required class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" placeholder="Ulangi password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit"
                                class="inline-flex justify-center rounded-xl px-5 py-2 text-sm font-semibold text-white bg-bedas-600 hover:bg-bedas-700 shadow-lg shadow-bedas-200 transition-all transform hover:-translate-y-0.5">
                                Tambah Pengguna
                            </button>
                            <button type="button" @click="showAddModal = false"
                                class="inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- EDIT USER MODAL --}}
        {{-- ========================================= --}}
        <div x-show="showEditModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity" @click="showEditModal = false">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                    <template x-if="editUser">
                        <form method="POST" :action="'/petugas/users/' + editUser.id">
                            @csrf
                            @method('PUT')
                            <div class="bg-white px-6 pt-6 pb-4">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-900">Edit Pengguna</h3>
                                        <p class="text-xs text-slate-500">Perbarui data pengguna. Kosongkan password jika tidak ingin diubah.</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" x-model="editUser.name" required class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Email <span class="text-red-500">*</span></label>
                                        <input type="email" name="email" x-model="editUser.email" required class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">No. WhatsApp</label>
                                            <input type="text" name="phone" x-model="editUser.phone" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">NIP</label>
                                            <input type="text" name="nip" x-model="editUser.nip" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm">
                                        </div>
                                    </div>

                                    {{-- Role (read-only display) --}}
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Role</label>
                                        <div class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-600 capitalize" x-text="editUser.role"></div>
                                        <p class="text-[10px] text-slate-400 mt-1">Role tidak dapat diubah.</p>
                                    </div>

                                    {{-- Status Aktif (only for camat) --}}
                                    <template x-if="editUser.role === 'camat'">
                                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                            <label class="flex items-center justify-between cursor-pointer">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800">Status Akun Camat</p>
                                                    <p class="text-xs text-slate-500 mt-0.5">Nonaktifkan saat pergantian Camat. Dokumen yang sudah ditandatangani tetap tersimpan.</p>
                                                </div>
                                                <div class="relative ml-4 shrink-0">
                                                    <input type="hidden" name="is_active" value="0">
                                                    <input type="checkbox" name="is_active" value="1" x-model="editUser.is_active" class="sr-only peer">
                                                    <div @click="editUser.is_active = !editUser.is_active" class="w-11 h-6 bg-slate-300 rounded-full peer peer-checked:bg-green-500 transition-colors cursor-pointer"
                                                        :class="editUser.is_active ? 'bg-green-500' : 'bg-slate-300'">
                                                        <div class="absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full transition-transform shadow-sm"
                                                            :class="editUser.is_active ? 'translate-x-5' : 'translate-x-0'"></div>
                                                    </div>
                                                </div>
                                            </label>
                                            <div class="mt-2 flex items-center gap-1.5">
                                                <template x-if="editUser.is_active">
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-green-600">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Aktif — Camat dapat login dan menandatangani dokumen
                                                    </span>
                                                </template>
                                                <template x-if="!editUser.is_active">
                                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-red-500">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Nonaktif — Camat tidak dapat login
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Password Baru</label>
                                            <input type="password" name="password" x-model="editUser.password" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" placeholder="Kosongkan jika tidak diubah">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">Konfirmasi</label>
                                            <input type="password" name="password_confirmation" x-model="editUser.password_confirmation" class="w-full border-slate-300 rounded-lg focus:ring-bedas-500 focus:border-bedas-500 text-sm" placeholder="Ulangi password baru">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                                <button type="submit"
                                    class="inline-flex justify-center rounded-xl px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                                    Simpan Perubahan
                                </button>
                                <button type="button" @click="showEditModal = false"
                                    class="inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- DELETE CONFIRMATION MODAL --}}
        {{-- ========================================= --}}
        <div x-show="showDeleteModal" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity" @click="showDeleteModal = false">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">

                    <template x-if="deleteUser">
                        <form method="POST" :action="'/petugas/users/' + deleteUser.id">
                            @csrf
                            @method('DELETE')
                            <div class="bg-white px-6 pt-6 pb-4">
                                <div class="flex items-center justify-center w-14 h-14 mx-auto mb-4 rounded-full bg-red-100 text-red-600">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
                                </div>

                                <div class="text-center">
                                    <h3 class="text-xl font-bold text-slate-900 mb-2">Hapus Pengguna</h3>
                                    <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus akun:</p>
                                    <p class="text-base font-bold text-slate-800 mt-2" x-text="deleteUser.name"></p>
                                    <p class="text-xs text-slate-400 mt-0.5" x-text="deleteUser.email"></p>

                                    <template x-if="deleteUser.role === 'camat'">
                                        <div class="mt-3 bg-amber-50 border border-amber-200 rounded-lg p-3">
                                            <p class="text-xs text-amber-700 font-medium">⚠️ Jika Camat ini sudah menandatangani dokumen, gunakan fitur <strong>Nonaktifkan</strong> melalui Edit, bukan hapus.</p>
                                        </div>
                                    </template>

                                    <p class="text-xs text-red-500 mt-3 font-medium">Tindakan ini tidak dapat dibatalkan.</p>
                                </div>
                            </div>

                            <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-3">
                                <button type="submit"
                                    class="inline-flex justify-center rounded-xl px-5 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 shadow-lg shadow-red-200 transition-all">
                                    Ya, Hapus
                                </button>
                                <button type="button" @click="showDeleteModal = false"
                                    class="inline-flex justify-center rounded-xl border border-slate-300 px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
