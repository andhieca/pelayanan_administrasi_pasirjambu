<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MasyarakatController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\CamatController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    $articles = \App\Models\Article::latest()->take(3)->get();
    return view('welcome', compact('articles'));
});

// Temporary route to fix live server configuration
Route::get('/setup', function () {
    try {
        // Directly add the column without relying on migration files being uploaded
        if (!\Illuminate\Support\Facades\Schema::hasColumn('permohonans', 'verification_token')) {
            \Illuminate\Support\Facades\Schema::table('permohonans', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->uuid('verification_token')->nullable()->after('status')->unique();
            });
        }

        // Add phone column to users table
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'phone')) {
            \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('phone', 20)->nullable()->after('email');
            });
        }

        // Add is_active column to users table
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active')) {
            \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('role');
            });
        }
        
        // Backfill tokens for old documents that don't have one
        $permohonans = \App\Models\Permohonan::whereNull('verification_token')->get();
        $count = 0;
        foreach ($permohonans as $p) {
            $p->update(['verification_token' => (string) \Illuminate\Support\Str::uuid()]);
            $count++;
        }
        
        // Link storage
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        
        // Clear caches
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        
        return 'Setup completed successfully! Database migrated, storage linked, and cache cleared.';
    } catch (\Exception $e) {
        return 'Error during setup: ' . $e->getMessage();
    }
});

// TEMPORARY: Halaman diagnostik untuk debug gambar - HAPUS setelah selesai!
Route::get('/debug-images', function () {
    $articles = \App\Models\Article::select('id', 'title', 'image')->get();
    $output = '<h2>🔍 Diagnostik Gambar Artikel</h2>';
    $output .= '<p><strong>storage_path("app/public"):</strong> ' . storage_path('app/public') . '</p>';
    $output .= '<p><strong>public_path():</strong> ' . public_path() . '</p>';
    $output .= '<p><strong>base_path():</strong> ' . base_path() . '</p>';
    $output .= '<p><strong>Folder storage/app/public ada?</strong> ' . (is_dir(storage_path('app/public')) ? '✅ YA' : '❌ TIDAK') . '</p>';
    $output .= '<p><strong>Folder storage/app/public/articles ada?</strong> ' . (is_dir(storage_path('app/public/articles')) ? '✅ YA' : '❌ TIDAK') . '</p>';

    if (is_dir(storage_path('app/public/articles'))) {
        $files = scandir(storage_path('app/public/articles'));
        $files = array_diff($files, ['.', '..']);
        $output .= '<p><strong>File di storage/app/public/articles/:</strong> ' . (count($files) > 0 ? implode(', ', $files) : '(kosong)') . '</p>';
    }

    $output .= '<p><strong>Folder public/uploads/articles ada?</strong> ' . (is_dir(public_path('uploads/articles')) ? '✅ YA' : '❌ TIDAK') . '</p>';

    if (is_dir(public_path('uploads/articles'))) {
        $files = scandir(public_path('uploads/articles'));
        $files = array_diff($files, ['.', '..']);
        $output .= '<p><strong>File di public/uploads/articles/:</strong> ' . (count($files) > 0 ? implode(', ', $files) : '(kosong)') . '</p>';
    }

    $output .= '<hr><h3>Data Artikel:</h3>';
    foreach ($articles as $article) {
        $output .= '<div style="border:1px solid #ccc; padding:10px; margin:10px 0; border-radius:8px;">';
        $output .= '<p><strong>ID:</strong> ' . $article->id . ' | <strong>Judul:</strong> ' . e($article->title) . '</p>';
        $output .= '<p><strong>Kolom image di DB:</strong> <code>' . e($article->image ?? '(NULL)') . '</code></p>';

        if ($article->image) {
            $locations = [
                'storage_path("app/public/' . $article->image . '")' => storage_path('app/public/' . $article->image),
                'public_path("uploads/' . $article->image . '")' => public_path('uploads/' . $article->image),
                'public_path("storage/' . $article->image . '")' => public_path('storage/' . $article->image),
            ];
            foreach ($locations as $label => $fullPath) {
                $exists = file_exists($fullPath);
                $output .= '<p>' . ($exists ? '✅' : '❌') . ' ' . e($label) . ' → <code>' . e($fullPath) . '</code></p>';
            }
            $output .= '<p><strong>image_url accessor:</strong> <code>' . e($article->image_url) . '</code></p>';
            $output .= '<p><strong>Test gambar:</strong> <img src="' . e($article->image_url) . '" style="max-width:200px; max-height:150px; border:1px solid #ddd; border-radius:8px;" onerror="this.alt=\'❌ GAGAL LOAD\'"></p>';
        }
        $output .= '</div>';
    }
    return $output;
});

// Robust file serving route (bypasses symlink issues on shared hosting)
Route::get('/berkas/{path}', function($path) {
    $path = str_replace(['..', '\\'], '', $path); // Prevent directory traversal

    // Cek di beberapa kemungkinan lokasi penyimpanan
    $locations = [
        storage_path('app/public/' . $path),
        public_path('uploads/' . $path),
        public_path('storage/' . $path),
    ];

    foreach ($locations as $fullPath) {
        if (file_exists($fullPath)) {
            return response()->file($fullPath);
        }
    }

    abort(404);
})->where('path', '.*')->name('berkas.serve');

Route::get('/berita', [\App\Http\Controllers\ArticlePublicController::class, 'index'])->name('public.articles.index');

// Public document verification (accessible without login)
Route::get('/verify/{token}', [VerificationController::class, 'verify'])->name('dokumen.verify');

Route::get('/dashboard', function () {
    $role = Auth::user()->role;
    if ($role === 'petugas') {
        return redirect()->route('petugas.dashboard');
    } elseif ($role === 'camat') {
        return redirect()->route('camat.dashboard');
    } else {
        return redirect()->route('masyarakat.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:masyarakat'])->group(function () {
    Route::get('/masyarakat/dashboard', [MasyarakatController::class, 'index'])->name('masyarakat.dashboard');
    Route::post('/masyarakat/permohonan', [MasyarakatController::class, 'store'])->name('masyarakat.store');
    Route::put('/masyarakat/permohonan/{id}', [MasyarakatController::class, 'update'])->name('masyarakat.update');
    Route::delete('/masyarakat/permohonan/{id}', [MasyarakatController::class, 'destroy'])->name('masyarakat.destroy');
    Route::get('/masyarakat/print/{id}', [MasyarakatController::class, 'print'])->name('masyarakat.print');
});

Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/petugas/dashboard', [PetugasController::class, 'index'])->name('petugas.dashboard');
    Route::post('/petugas/validate/{id}', [PetugasController::class, 'validateBerkas'])->name('petugas.validate');
    Route::post('/petugas/nomor-surat/{id}', [PetugasController::class, 'nomorSurat'])->name('petugas.nomorSurat');
    Route::resource('/petugas/articles', \App\Http\Controllers\ArticleController::class)->names('petugas.articles');
    Route::get('/petugas/users', [UserManagementController::class, 'index'])->name('petugas.users.index');
    Route::post('/petugas/users', [UserManagementController::class, 'store'])->name('petugas.users.store');
    Route::put('/petugas/users/{id}', [UserManagementController::class, 'update'])->name('petugas.users.update');
    Route::delete('/petugas/users/{id}', [UserManagementController::class, 'destroy'])->name('petugas.users.destroy');
});

Route::middleware(['auth', 'role:camat'])->group(function () {
    Route::get('/camat/dashboard', [CamatController::class, 'index'])->name('camat.dashboard');
    Route::post('/camat/approve/{id}', [CamatController::class, 'approve'])->name('camat.approve');
    Route::get('/camat/preview/{id}', [CamatController::class, 'preview'])->name('camat.preview');
    Route::get('/camat/report/print', [CamatController::class, 'printReport'])->name('camat.report.print');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
