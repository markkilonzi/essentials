<?php

namespace Nyumbani\Essentials\Console\Commands;

use Illuminate\Console\Command;
use Nyumbani\Essentials\Database\Seeders\PermissionSeeder;

class PermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nyumbani:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync roles and permissions from permissions.json, giving new permissions to existing users and deleting removed ones';

    /**
     * Execute the console command.
     *
     * @author Alvin G. Kaburu <geekaburu@nyumbanitech.co.ke>
     */
    public function handle(): int
    {
        //  Run the permission seeder
        (new PermissionSeeder)->run();

        //  Report success
        $this->components->info('Permissions updated.');

        return self::SUCCESS;
    }
}
