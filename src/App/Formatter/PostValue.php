<?php

/**
 * Ushahidi API Formatter for Post Values
 *
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

class PostValue extends API
{
    protected $map = [];

    public function __construct($map = [])
    {
        $this->map = $map;
    }

    public function __invoke($entity)
    {
        if (isset($this->map[$entity->type])) {
            $formatter = $this->map[$entity->type];
            return $formatter($entity);
        }

        return $entity->value;
    }
}
