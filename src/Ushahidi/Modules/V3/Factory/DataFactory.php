<?php

/**
 * Ushahidi Platform Factory for Data Transfer Objects
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Factory;

class DataFactory
{
    /**
     * Array of data transfer object names, mapped by action:
     *
     *     $actions = [
     *         'search' => $di->lazyNew('Namespace\To\Data\SearchData'),
     *         ...
     *     ];
     *
     * @var array
     */
    protected $actions = [];

    /**
     * @param array $actions
     */
    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    /**
     * Gets a new data transfer object from the map by type:
     *
     *     $search = $data->get('search', $params);
     *
     * @param  string $action
     * @param  array  $params
     * @return \Ushahidi\Core\Tool\Data
     */
    public function get($action, array $params = null)
    {
        if (empty($this->actions[$action])) {
            throw new \InvalidArgumentException(sprintf(
                'Data type %s is not defined',
                $action
            ));
        }
        $factory = $this->actions[$action];
        return $factory($params);
    }
}
