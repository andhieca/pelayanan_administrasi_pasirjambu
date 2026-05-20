<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Artikel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-100 p-8">
                <form action="{{ route('petugas.articles.update', $article->id) }}" method="POST"
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Judul Artikel</label>
                        <input type="text" name="title" value="{{ old('title', $article->title) }}" required
                            class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500 transition-all outline-none"
                            placeholder="Masukkan judul yang menarik...">
                        @error('title') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                            <select name="category"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-bedas-100 focus:border-bedas-500 transition-all outline-none bg-white">
                                <option value="Pembangunan" {{ (old('category', $article->category) == 'Pembangunan') ? 'selected' : '' }}>Pembangunan</option>
                                <option value="Warga" {{ (old('category', $article->category) == 'Warga') ? 'selected' : '' }}>Kegiatan Warga</option>
                                <option value="Kesehatan" {{ (old('category', $article->category) == 'Kesehatan') ? 'selected' : '' }}>Kesehatan</option>
                                <option value="Pendidikan" {{ (old('category', $article->category) == 'Pendidikan') ? 'selected' : '' }}>Pendidikan</option>
                                <option value="Pengumuman" {{ (old('category', $article->category) == 'Pengumuman') ? 'selected' : '' }}>Pengumuman Resmi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Gambar Sampul</label>
                            @if($article->image)
                                <div class="mb-3">
                                    <img src="{{ filter_var($article->image, FILTER_VALIDATE_URL) ? $article->image : asset('storage/' . $article->image) }}"
                                        class="w-32 h-20 object-cover rounded-xl border border-slate-100 shadow-sm"
                                        alt="Current Image">
                                    <p class="text-[10px] text-slate-400 mt-1">Gambar saat ini</p>
                                </div>
                            @endif
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
                            placeholder="Tulis isi artikel di sini...">{{ old('content', $article->content) }}</textarea>
                        @error('content') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 flex items-center gap-4">
                        <button type="submit"
                            class="px-8 py-4 bg-bedas-600 text-white font-bold rounded-2xl shadow-xl shadow-bedas-100 hover:bg-bedas-700 transition-all transform hover:-translate-y-1 active:scale-95">
                            Simpan Perubahan
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