<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MasyarakatController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\CamatController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    $articles = \App\Models\Article::latest()->take(3)->get();
    return view('welcome', compact('articles'));
});

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
});

Route::middleware(['auth', 'role:camat'])->group(function () {
    Route::get('/camat/dashboard', [CamatController::class, 'index'])->name('camat.dashboard');
    Route::post('/camat/approve/{id}', [CamatController::class, 'approve'])->name('camat.approve');
    Route::get('/camat/preview/{id}', [CamatController::class, 'preview'])->name('camat.preview');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
