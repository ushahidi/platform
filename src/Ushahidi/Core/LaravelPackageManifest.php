<?php
/*
 * This file is adapted from the Laravel Plug and Play package.
 *
 * @copyright  2019 Eder Soares
 * @license  https://github.com/edersoares/laravel-plug-and-play/blob/master/license.md MIT License
 */

namespace Ushahidi\Core;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\PackageManifest;

class LaravelPackageManifest extends PackageManifest
{
    /**
     * Build the manifest and write it to disk. Read and write plug and play
     * packages.
     *
     * @throws FileNotFoundException
     *
     * @return void
     */
    public function build()
    {
        $packages = [];

        if ($this->files->exists($path = $this->vendorPath . '/composer/installed.json')) {
            $installed = json_decode($this->files->get($path), true);

            $packages = $installed['packages'] ?? $installed;
        }

        $customPackages = [];

        if ($this->files->exists($composerJson = $this->basePath . '/composer.json')) {
            $json = json_decode($this->files->get($composerJson), true);

            $include = $json['extra']['merge-plugin']['include'] ?? [];
            $require = $json['extra']['merge-plugin']['require'] ?? [];

            $customPackages = collect($include)->merge($require)->map(function ($package) {
                return glob($package);
            })->flatten()->filter(function ($package) {
                return $this->files->exists($package);
            })->map(function ($path) {
                return json_decode($this->files->get($path), true);
            })->mapWithKeys(function ($package) {
                return [$package['name'] => $package['extra']['laravel'] ?? []];
            })->filter()->all();
        }

        $ignoreAll = in_array('*', $ignore = $this->packagesToIgnore());

        $write = collect($packages)->mapWithKeys(function ($package) {
            return [$this->format($package['name']) => $package['extra']['laravel'] ?? []];
        })
        ->merge($customPackages)
        ->each(function ($configuration) use (&$ignore) {
            $ignore = array_merge($ignore, $configuration['dont-discover'] ?? []);
        })->reject(function ($configuration, $package) use ($ignore, $ignoreAll) {
            return $ignoreAll || in_array($package, $ignore);
        })->filter()->all();

        $this->write($write);
    }
}
