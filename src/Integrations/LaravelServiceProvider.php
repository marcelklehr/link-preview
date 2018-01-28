<?php
namespace Marcelklehr\LinkPreview\Integrations;

use Illuminate\Support\ServiceProvider;
use Marcelklehr\LinkPreview\Client;

/**
 * Class LaravelServiceProvider
 * @package Marcelklehr\LinkPreview\Integrations
 * @codeCoverageIgnore
 */
class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('link-preview', function() {
            return new Client();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['link-preview'];
    }
}
