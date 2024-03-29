<?php

namespace Laravelir\Attachmentable\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallPackageCommand extends Command
{
    protected $signature = 'attachmentable:install';

    protected $description = 'Install the attachmentable package';

    public function handle()
    {
        $this->line("\t... Welcome To Attachmentable Installer ...");

        //config
        if (File::exists(config_path('attachmentable.php'))) {
            $confirm = $this->confirm("attachmentable.php already exist. Do you want to overwrite?");
            if ($confirm) {
                $this->publishConfig();
                $this->info("config overwrite finished");
            } else {
                $this->info("skipped config publish");
            }
        } else {
            $this->publishConfig();
            $this->info("config published");
        }

        if (!empty(File::glob(database_path('migrations\*_create_attachmentables_tables.php')))) {

            $list  = File::glob(database_path('migrations\*_create_attachmentables_tables.php'));
            collect($list)->each(function ($item) {
                File::delete($item);
            });

            $this->publishMigration();
        } else {
            $this->publishMigration();
        }

        $this->info("Attachmentable Successfully Installed.\n");
        $this->info("\t\tStar me on Github");
    }

    private function publishMigration()
    {
        $this->call('vendor:publish', [
            '--provider' => "Laravelir\Attachmentable\Providers\AttachmentableServiceProvider",
            '--tag'      => 'attachmentable-migrations',
            '--force'    => true
        ]);
    }

    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => "Laravelir\Attachmentable\Providers\AttachmentableServiceProvider",
            '--tag'      => 'attachmentable-config',
            '--force'    => true
        ]);
    }

}
