<?php

namespace Laravelir\Attachmentable\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Laravelir\Attachmentable\Facades\AttachmentableFacade;
use Laravelir\Attachmentable\Console\Commands\InstallPackageCommand;

class AttachmentableServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/attachmentable.php", 'attachmentable');

        $this->registerFacades();

        $this->publishMigrations();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    private function registerFacades()
    {
        $this->app->bind('attachmentable', function ($app) {
            return new AttachmentableFacade();
        });
    }

    private function registerCommands()
    {
        $this->commands([
            InstallPackageCommand::class,
        ]);
    }

    public function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../../config/attachmentable.php' => config_path('attachmentable.php')
        ], 'attachmentable-config');
    }

    protected function publishMigrations()
    {
        if (empty(File::glob(database_path('migrations/*_create_attachmentables_tables.php')))) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../../database/migrations/create_attachmentables_table.stub.php' => database_path() . "/migrations/{$timestamp}_create_attachmentables_tables.php",
            ], 'attachmentable-migrations');

        } else {
            $list  = File::glob(database_path('migrations\*_create_attachmentables_tables.php'));
            collect($list)->each(function ($item) {
                File::delete($item);
            });

            $this->publishMigrations();
        }
    }
}
