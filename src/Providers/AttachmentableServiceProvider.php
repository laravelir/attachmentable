<?php

namespace Laravelir\Attachmentable\Providers;

use Illuminate\Support\ServiceProvider;
use Laravelir\Attachmentable\Console\Commands\InstallPackageCommand;
use Laravelir\Attachmentable\Facades\AttachmentableFacade;

class AttachmentableServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/attachmentable.php", 'attachmentable');

        $this->registerFacades();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->registerPublishes();
            $this->publishConfig();
        }
    }

    private function registerFacades()
    {
        $this->app->bind('attachmentable', function ($app) {
            return new AttachmentableFacade();
        });
    }

    private function registerPublishes()
    {
        $this->publishes([
            __DIR__ . '/../../config/attachmentable.php' => config_path('attachmentable.php')
        ], 'attachmentable-config');
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
}
