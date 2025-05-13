<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DeactivateInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:deactivate-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate users who have been inactive for more than 30 days and delete their tasks';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $thresholdDate = Carbon::now()->subDays(30);

        DB::transaction(function () use ($thresholdDate) {
            $users = User::where('updated_at', '<', $thresholdDate)
                ->where('status', 'active')
                ->get();

            foreach ($users as $user) {
                $user->status = 'inactive';
                $user->save();

                $user->tasks()->delete();

                $this->info("User ID {$user->id} deactivated and tasks deleted.");
            }
        });

        $this->info('Deactivation check completed.');
    }
}
