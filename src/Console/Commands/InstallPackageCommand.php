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


        $this->info("Attachmentable Successfully Installed.\n");
        $this->info("\t\tStar me on Github");
    }

    private function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => "Laravelir\Attachmentable\Providers\AttachmentableServiceProvider",
            '--tag'      => 'attachmentable-config',
            '--force'    => true
        ]);
    }

    //     //assets
    //     if (File::exists(public_path('attachmentable'))) {
    //         $confirm = $this->confirm("attachmentable directory already exist. Do you want to overwrite?");
    //         if ($confirm) {
    //             $this->publishAssets();
    //             $this->info("assets overwrite finished");
    //         } else {
    //             $this->info("skipped assets publish");
    //         }
    //     } else {
    //         $this->publishAssets();
    //         $this->info("assets published");
    //     }

    //     //migration
    //     if (File::exists(database_path("migrations/$migrationFile"))) {
    //         $confirm = $this->confirm("migration file already exist. Do you want to overwrite?");
    //         if ($confirm) {
    //             $this->publishMigration();
    //             $this->info("migration overwrite finished");
    //         } else {
    //             $this->info("skipped migration publish");
    //         }
    //     } else {
    //         $this->publishMigration();
    //         $this->info("migration published");
    //     }

    //     $this->call('migrate');
    // }


    // private function publishMigration()
    // {
    //     $this->call('vendor:publish', [
    //         '--provider' => "Laravelir\Attachmentable\Providers\AttachmentableServiceProvider",
    //         '--tag'      => 'migrations',
    //         '--force'    => true
    //     ]);
    // }

    // private function publishAssets()
    // {
    //     $this->call('vendor:publish', [
    //         '--provider' => "Laravelir\Attachmentable\Providers\AttachmentableServiceProvider",
    //         '--tag'      => 'assets',
    //         '--force'    => true
    //     ]);
    // }
}
