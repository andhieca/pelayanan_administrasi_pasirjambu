<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tulis Artikel Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">
                <form action="{{ route('petugas.articles.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Judul Artikel</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500 transition-all outline-none"
                            placeholder="Masukkan judul yang menarik...">
                        @error('title') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                            <select name="category"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500 transition-all outline-none bg-white">
                                <option value="Pembangunan">Pembangunan</option>
                                <option value="Warga">Kegiatan Warga</option>
                                <option value="Kesehatan">Kesehatan</option>
                                <option value="Pendidikan">Pendidikan</option>
                                <option value="Pengumuman">Pengumuman Resmi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Gambar Sampul</label>
                            <input type="file" name="image" accept="image/*"
                                class="w-full px-4 py-2 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500 transition-all outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-bedas-50 file:text-bedas-700 hover:file:bg-bedas-100">
                            @error('image') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Konten Artikel</label>
                        <textarea name="content" rows="10" required
                            class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500 transition-all outline-none"
                            placeholder="Tulis isi artikel di sini...">{{ old('content') }}</textarea>
                        @error('content') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 flex items-center gap-4">
                        <button type="submit"
                            class="px-8 py-4 bg-bedas-600 text-white font-bold rounded-2xl shadow-xl shadow-bedas-100 hover:bg-bedas-700 transition-all transform hover:-translate-y-1 active:scale-95">
                            Terbitkan Artikel
                        </button>
                        <a href="{{ route('petugas.articles.index') }}"
                            class="text-slate-500 font-bold hover:text-slate-700 transition-colors">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>