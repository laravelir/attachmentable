<?php

namespace Laravelir\Attachmentable\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

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

    // protected function publishMigrations()
    // {
    //     if (empty(File::glob(database_path('migrations/*_create_attachmentables_tables.php')))) {

    //         $this->publishes([
    //             __DIR__ . '/../../database/migrations/create_attachmentables_table.stub.php' => database_path() . "/migrations//" . date('Y_m_d_His', time()) . "_create_attachmentables_tables.php",
    //         ], 'attachmentable-migrations');
    //     } else {
    //         $list  = File::glob(database_path('migrations\*_create_attachmentables_tables.php'));
    //         collect($list)->each(function ($item) {
    //             File::delete($item);
    //             dd("delete");
    //         });

    //         Artisan::call('php artisan vendor:publish --provider=Laravelir\\Attachmentable\\Providers\\AttachmentableServiceProvider --tag=attachmentable-migrations --force');
    //     }
    // }
}
