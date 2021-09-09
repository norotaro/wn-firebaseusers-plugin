<?php

namespace Norotaro\FirebaseUsers\Console;

use Illuminate\Console\Command;
use Norotaro\FirebaseAuth\Classes\UserHelper;
use Norotaro\FirebaseUsers\Models\Settings;
use Symfony\Component\Console\Input\InputOption;

class SyncUsers extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'firebaseusers:sync';

    /**
     * @var string The console command description.
     */
    protected $description = 'Sync users from Firebase.';

    public $auth;

    function __construct(\Kreait\Firebase\Auth $auth)
    {
        parent::__construct();
        $this->auth = $auth;
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $this->line("Starting synchronization...\n");
        $bar = $this->output->createProgressBar();
        $bar->start();
        \Db::table('users')->update(['fb_sync' => false]);

        $users = $this->auth->listUsers($this->option('max-results'), $this->option('batch-size'));
        $batch = [];
        $usersCount = 0;
        foreach ($users as $user) {
            /** @var \Kreait\Firebase\Auth\UserRecord $user */
            $batch[] = UserHelper::instance()->userToArray($user);

            // sync users with batch of 100 rows
            if (count($batch) >= 100) {
                $this->processBatch($batch);
                $batch = [];
            }

            $bar->advance();
            $usersCount++;
        }

        if (count($batch)) {
            $this->processBatch($batch);
        }

        $bar->finish();

        $this->info("\n\nSynchronization completed. Deleting remaining users...");
        $deletedUsers = \Db::table('users')->where('fb_sync', false)->delete();
        $this->comment($deletedUsers ? "Deleted $deletedUsers user/s" : 'No user was deleted');

        // print summary
        $this->table(['Summary', 'Count'], [
            ['Synchronized users', $usersCount],
            ['Deleted users', $deletedUsers],
        ]);
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        $defaultMaxResults = Settings::get('sync_max_results', 1000);
        $defaultBatchSize = Settings::get('sync_batch_size', 1000);

        return [
            ['max-results', 'm', InputOption::VALUE_OPTIONAL, 'Maximum number of results to get from Firebase', $defaultMaxResults],
            ['batch-size', 'b', InputOption::VALUE_OPTIONAL, 'Number of results obtained in each batch', $defaultBatchSize],
        ];
    }

    protected function processBatch(array $batch): void
    {
        \Db::table('users')
            ->upsert($batch, ['fb_uid'], [
                'name',
                'email',
                'is_activated',
                'activated_at',
                'last_login',
                'updated_at',
                'username',
                'last_seen',
                'fb_sync',
            ]);
    }
}
