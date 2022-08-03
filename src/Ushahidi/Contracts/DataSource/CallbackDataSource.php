<?php

namespace Ushahidi\Contracts\DataSource;

use Illuminate\Routing\Router;

/**
 * Data Source interface for data source that communicate via callbacks
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    DataSource
 * @copyright  2013 Ushahidi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */
interface CallbackDataSource extends DataSource
{

    /**
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public static function registerRoutes(Router $router);
}
