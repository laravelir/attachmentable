<?php

namespace Laravelir\Attachmentable\Providers;

use Illuminate\Support\ServiceProvider;
use Laravelir\Attachmentable\Facades\AttachmentableFacade;
use Laravelir\Attachmentable\Console\Commands\InstallPackageCommand;

class AttachmentableServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/attachmentable.php", 'attachmentable');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->registerFacades();
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
            $this->publishMigrations();
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
        $this->publishes([
            __DIR__ . '/../../database/migrations/create_attachmentables_table.stub' => database_path() . "/migrations//" . date('Y_m_d_His', time()) . "_create_attachmentables_table.php",
        ], 'attachmentable-migrations');
    }
}
