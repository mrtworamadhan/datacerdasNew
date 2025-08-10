<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-user-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Menyinkronkan role untuk semua user...');
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            // Cek user_type dan berikan role Spatie yang sesuai
            // syncRoles akan menghapus role lama dan memberi yang baru, jadi aman dijalankan berkali-kali
            if ($user->user_type === 'admin_desa') {
                $user->syncRoles('admin_desa');
            } elseif ($user->user_type === 'kader_posyandu') {
                $user->syncRoles('kader_posyandu');
            } elseif ($user->user_type === 'admin_rw') {
                $user->syncRoles('admin_rw');
            } elseif ($user->user_type === 'admin_rt') {
                $user->syncRoles('admin_rt');
            }
            // Tambahkan user type lain jika ada
        }
        $this->info('✅ Sinkronisasi role selesai!');
    }
}
