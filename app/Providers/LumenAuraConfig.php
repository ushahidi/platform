<?php

namespace Ushahidi\App\Providers;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;
use Illuminate\Support\Facades\DB;

class LumenAuraConfig extends ContainerConfig
{
    public function define(Container $di)
    {
        $this->configureAuraServices($di);
        $this->injectAuraConfig($di);
    }

    protected function configureAuraServices(Container $di)
    {
        // Configure mailer
        $di->set('tool.mailer', $di->lazyNew('Ushahidi\App\Tools\LumenMailer', [
            'mailer' => app('mailer'),
            'siteConfig' => $di->lazyGet('site.config'),
            'clientUrl' => $di->lazyGet('clienturl')
        ]));

        // Configure filesystem
        // The Ushahidi filesystem adapter returns a flysystem adapter for a given
        // cdn type based on the provided configuration
        $di->set('tool.filesystem', function () {
            // Get the underlying League\Flysystem\Filesystem instance
            return app('filesystem')->disk()->getDriver();
        });

        // Setup user session service
        $di->set('session', $di->lazyNew(\Ushahidi\App\Tools\LumenSession::class, [
            'userRepo' => $di->lazyGet('repository.user')
        ]));

        $di->set('db.eloquent.resolver', $di->lazy(function () {
            return app('db');
        }));

        // Abstract repository parameters
        $di->set('db.ohanzee.resolver', $di->lazy(function () {
            return app(\Ushahidi\App\Multisite\OhanzeeResolver::class);
        }));

        // Configure dispatcher
        $di->setters[\Ushahidi\Core\Traits\Events\DispatchesEvents::class]['setDispatcher']
            = app('events');
    }

    protected function injectAuraConfig(Container $di)
    {
        // CDN Config settings
        $di->set('cdn.config', function () use ($di) {
            return config('cdn');
        });

        // Ratelimiter config settings
        $di->set('ratelimiter.config', function () use ($di) {
            return config('ratelimiter');
        });

        // Multisite db
        // Move multisite enabled check to class and move to src/App
        $di->set('site', function () use ($di) {
            // @todo default to using the current domain
            $site = '';

            // Is this a multisite install?
            $multisite = config('multisite.enabled');
            if ($multisite) {
                $site = $di->get('multisite')->getSite();
            }

            return $site;
        });

        // Move multisite enabled check to class and move to src/App
        $di->set('tool.uploader.prefix', function () use ($di) {
            // Is this a multisite install?
            $multisite = config('multisite.enabled');
            if ($multisite) {
                return $di->get('multisite')->getCdnPrefix();
            }

            return '';
        });

        // Client Url
        $di->set('clienturl', function () use ($di) {
            return $this->getClientUrl($di->get('site.config'), $di->lazyGet('multisite'));
        });
    }

    protected function getClientUrl($config, $multisite)
    {
        $clientUrl = env('CLIENT_URL', false);

        if (env("MULTISITE_DOMAIN", false)) {
            try {
                $clientUrl = $multisite()->getClientUrl();
            } catch (Exception $e) {
            }
        }

        // Or overwrite from config
        if (!$clientUrl && $config['client_url']) {
            $client_url = $config['client_url'];
        }

        return $clientUrl;
    }
}
