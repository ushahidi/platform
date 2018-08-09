<?php

/**
 * Ushahidi Post Value Validator Factory
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Post;

class ValueFactory
{
    // a map of value type to factory closures
    protected $map = [];

    public function __construct($map = [])
    {
        $this->map = $map;
    }

    public function getValidator($type)
    {
        return isset($this->map[$type]) ? $this->map[$type]() : false;
    }
}
