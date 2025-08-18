<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Desa;
use App\Models\RW;
use App\Models\RT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@datacerdas.com'],
            [
                'name' => 'Super Admin DataCerdas',
                'password' => Hash::make('password'),
                'user_type' => 'super_admin',
                'desa_id' => null,
            ]
        );
    }
}
