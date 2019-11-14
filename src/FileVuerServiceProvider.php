<?php

namespace Jwhulette\Filevuer;

use Illuminate\Support\ServiceProvider;
use jwhulette\filevuer\services\FileService;
use jwhulette\filevuer\services\UploadService;
use jwhulette\filevuer\middleware\SessionDriver;
use jwhulette\filevuer\services\DownloadService;
use jwhulette\filevuer\services\DirectoryService;
use jwhulette\filevuer\services\ConnectionService;
use jwhulette\filevuer\services\ConfigurationService;
use jwhulette\filevuer\services\FileServiceInterface;
use jwhulette\filevuer\services\UploadServiceInterface;
use jwhulette\filevuer\services\DownloadServiceInterface;
use jwhulette\filevuer\services\DirectoryServiceInterface;
use jwhulette\filevuer\services\ConnectionServiceInterface;
use jwhulette\filevuer\services\ConfigurationServiceInterface;

class FilevuerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/filevuer.php' => config_path('filevuer.php'),
            __DIR__ . '/resources/views' => resource_path('views/vendor/filevuer'),
            __DIR__ . '/public' => public_path('vendor/filevuer')
        ], 'filevuer');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'filevuer');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app['router']->aliasMiddleware('sessionDriver', SessionDriver::class);
        $this->app->bind(ConfigurationServiceInterface::class, ConfigurationService::class);
        $this->app->bind(ConnectionServiceInterface::class, ConnectionService::class);
        $this->app->bind(DirectoryServiceInterface::class, DirectoryService::class);
        $this->app->bind(FileServiceInterface::class, FileService::class);
        $this->app->bind(DownloadServiceInterface::class, DownloadService::class);
        $this->app->bind(UploadServiceInterface::class, UploadService::class);
    }
}
