<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class SyncUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync user roles from user_type column to Spatie roles table for existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting user role synchronization...');

        $users = User::all();
        $syncedCount = 0;
        $skippedCount = 0;

        // Buat progress bar agar terlihat keren dan informatif
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $userType = $user->user_type;

            // Pastikan user_type tidak kosong dan role-nya ada di database
            if ($userType && Role::where('name', $userType)->exists()) {
                // syncRoles akan menghapus role lama (jika ada) dan menerapkan yang baru.
                // Ini aman untuk dijalankan berkali-kali.
                $user->syncRoles([$userType]);
                $syncedCount++;
            } else {
                $this->warn("\nSkipping user {$user->email} - Role '{$userType}' not found or empty.");
                $skippedCount++;
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n"); // Baris baru setelah progress bar
        $this->info('Synchronization complete!');
        $this->info("{$syncedCount} users have been synchronized.");
        $this->info("{$skippedCount} users were skipped.");

        return 0;
    }
}
