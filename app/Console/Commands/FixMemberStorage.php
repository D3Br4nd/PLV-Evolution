<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixMemberStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plv:fix-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure all members have the correct storage directory structure (avatar, documents)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting checks on member storage directories...');

        $users = User::all();
        $disk = Storage::disk('public');
        $count = 0;

        foreach ($users as $user) {
            $userDir = $user->id; // uuid

            $avatarDir = $userDir . '/avatar';
            $docsDir = $userDir . '/documents';

            $fixed = false;

            if (!$disk->exists($avatarDir)) {
                $disk->makeDirectory($avatarDir);
                $this->line("Created avatar directory for user {$user->id} ({$user->email})");
                $fixed = true;
            }

            if (!$disk->exists($docsDir)) {
                $disk->makeDirectory($docsDir);
                $this->line("Created documents directory for user {$user->id} ({$user->email})");
                $fixed = true;
            }

            if ($fixed) {
                $count++;
            }
        }

        $this->info("Completed. Fixed directories for {$count} users.");
    }
}
