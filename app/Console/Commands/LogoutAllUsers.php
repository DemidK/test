<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Exception;

class LogoutAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:logout-all {--keep-admins : Keep superusers logged in}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force logout all users by clearing sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Starting forced logout of all users...');
            
            $keepAdmins = $this->option('keep-admins');
            
            if ($keepAdmins) {
                $this->info('Superusers will remain logged in.');
            }

            // Method 1: Clear the sessions table if using database sessions
            if (config('session.driver') === 'database') {
                $this->info('Clearing database sessions...');
                
                if ($keepAdmins) {
                    // Get superuser IDs to preserve their sessions
                    $superuserIds = $this->getSuperuserIds();
                    
                    if (!empty($superuserIds)) {
                        $query = DB::table('sessions');
                        foreach ($superuserIds as $id) {
                            $query->where('user_id', '!=', $id);
                        }
                        $count = $query->delete();
                        $this->info("{$count} database sessions cleared (keeping superusers).");
                    } else {
                        DB::table('sessions')->truncate();
                        $this->info('All database sessions cleared (no superusers found).');
                    }
                } else {
                    DB::table('sessions')->truncate();
                    $this->info('All database sessions cleared.');
                }
            } 
            // Method 2: Clear session files if using file sessions
            else if (config('session.driver') === 'file') {
                $this->info('Clearing session files...');
                $sessionPath = config('session.files');
                $files = glob($sessionPath . '/*');
                $count = 0;
                
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                        $count++;
                    }
                }
                
                $this->info("{$count} session files cleared.");
            }
            // Method 3: Clear cache if using cache or redis for sessions
            else {
                $this->info('Clearing cache...');
                Artisan::call('cache:clear');
                $this->info('Cache cleared.');
                
                if (config('session.driver') === 'redis') {
                    $this->info('Clearing Redis sessions...');
                    Artisan::call('redis:flushdb');
                    $this->info('Redis sessions cleared.');
                }
            }

            // Method 4: You could also invalidate remember tokens for forced re-login
            if ($this->confirm('Do you want to invalidate "remember me" tokens as well?')) {
                $this->info('Invalidating remember tokens...');
                
                if ($keepAdmins) {
                    $superuserIds = $this->getSuperuserIds();
                    $query = DB::table('user')->whereNotIn('id', $superuserIds);
                    $query->update(['remember_token' => null]);
                } else {
                    DB::table('user')->update(['remember_token' => null]);
                }
                
                $this->info('Remember tokens invalidated.');
            }

            $this->info('All users have been successfully logged out.');
            Log::info('Admin forced logout of all users');
            
            return 0;
            
        } catch (Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            Log::error('Error in logout command: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Get IDs of superuser role users
     */
    protected function getSuperuserIds()
    {
        try {
            // Query to get users with superuser role
            return DB::table('user_role')
                ->join('roles', 'user_role.role_id', '=', 'roles.id')
                ->where('roles.slug', 'superuser')
                ->pluck('user_role.user_id')
                ->toArray();
        } catch (Exception $e) {
            $this->warn("Could not determine superusers: " . $e->getMessage());
            return [];
        }
    }
}