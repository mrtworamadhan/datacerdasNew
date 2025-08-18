<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AsetCodificationSeeder::class,
            AdminSeeder::class,
            DesaSeeder::class,
            MasterDataSeeder::class,
            // WargaDummySeeder::class, 
            KlasifikasiSuratSeeder::class,
        ]);
    }
}
