<?php

namespace Ushahidi\App\DataSource;

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

    public function registerRoutes(\Laravel\Lumen\Routing\Router $router);
}
