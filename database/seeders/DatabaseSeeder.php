<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Petugas (Admin)
        User::factory()->create([
            'name' => 'Petugas Admin',
            'email' => 'admin@pasirjambu.go.id',
            'password' => Hash::make('password'),
            'role' => 'petugas',
        ]);

        // Camat
        User::factory()->create([
            'name' => 'Bapak Camat',
            'email' => 'camat@pasirjambu.go.id',
            'password' => Hash::make('password'),
            'role' => 'camat',
        ]);

        // Masyarakat Dummy
        User::factory()->create([
            'name' => 'Warga Sipil',
            'email' => 'warga@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'masyarakat',
        ]);

        // Additional Dummy Masyarakat for testing FCFS
        User::factory()->create([
            'name' => 'Warga Antri 1',
            'email' => 'warga1@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'masyarakat',
        ]);
    }
}
