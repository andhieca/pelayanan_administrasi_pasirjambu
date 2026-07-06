<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Copy gambar dari storage ke folder public agar bisa diakses browser
     * (mengatasi masalah symlink di shared hosting seperti Hostinger)
     */
    private function syncImageToPublic(string $imagePath): void
    {
        $source = storage_path('app/public/' . $imagePath);
        $destination = public_path('storage/' . $imagePath);

        // Buat folder tujuan jika belum ada
        $destDir = dirname($destination);
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        // Copy file
        if (File::exists($source)) {
            File::copy($source, $destination);
        }
    }

    /**
     * Hapus gambar dari kedua lokasi (storage dan public)
     */
    private function deleteImage(string $imagePath): void
    {
        Storage::disk('public')->delete($imagePath);

        $publicPath = public_path('storage/' . $imagePath);
        if (File::exists($publicPath)) {
            File::delete($publicPath);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with('user')->latest()->paginate(10);
        return view('dashboard.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('articles', 'public');
            // Copy ke folder public agar bisa diakses tanpa symlink
            $this->syncImageToPublic($imagePath);
        }

        Article::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'content' => $request->input('content'),
            'category' => $request->category,
            'image' => $imagePath,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('petugas.articles.index')->with('success', 'Artikel berhasil diterbitkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article = Article::findOrFail($id);
        return view('dashboard.articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article = Article::findOrFail($id);

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'content' => $request->input('content'),
            'category' => $request->category,
        ];

        if ($request->hasFile('image')) {
            // Hapus gambar lama dari kedua lokasi
            if ($article->image) {
                $this->deleteImage($article->image);
            }
            $data['image'] = $request->file('image')->store('articles', 'public');
            // Copy ke folder public agar bisa diakses tanpa symlink
            $this->syncImageToPublic($data['image']);
        }

        $article->update($data);

        return redirect()->route('petugas.articles.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);

        if ($article->image) {
            $this->deleteImage($article->image);
        }

        $article->delete();

        return redirect()->route('petugas.articles.index')->with('success', 'Artikel berhasil dihapus!');
    }
}

