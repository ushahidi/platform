<?php

/**
 * Ushahidi Platform Bootstrap
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

// @codingStandardsIgnoreFile
// PHPCS doesn't like this file because it declares function AND executes logic

// For dependency management and autoloading, we use [Composer][composer].
//
// **If you haven't already done so, you should run `composer install` now.**
//
// [composer]: http://getcomposer.org/
require_once __DIR__ . '/../vendor/autoload.php';

// The global [Dependency Injection][di] container lives inside of a global
// `service()` function. This avoids the need to have a global variable, and
// allows for easy access to loading services by using the `$what` parameter.
//
// Currently, we use [Aura.Di][auradi] to power the container.
//
// [di]: https://en.wikipedia.org/wiki/Dependency_injection
// [auradi]: https://github.com/auraphp/Aura.Di/tree/develop-2
function service($what = null)
{
    static $di;
    if (!$di) {
        $builder = new Aura\Di\ContainerBuilder();
        $di = $builder->newConfiguredInstance([
            'Ushahidi\Core\CoreConfig',
            'Ushahidi\App\AppConfig',
            'Ushahidi\App\Providers\LumenAuraConfig',
            'Ushahidi\Console\ConsoleConfig',
        ]);
    }
    if ($what) {
        return $di->get($what);
    }
    return $di;
}
