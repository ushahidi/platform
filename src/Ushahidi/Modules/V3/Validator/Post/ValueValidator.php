<?php

/**
 * Ushahidi Post Value Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\Post;

abstract class ValueValidator /* implements Validator */
{
    protected $config;

    protected $default_error_source = 'post';

    public function setConfig(array $config = null)
    {
        $this->config = $config;
    }

    public function check(array $values)
    {
        foreach ($values as $value) {
            if ($error = $this->validate($value)) {
                return $error;
            }
        }
    }

    abstract protected function validate($value);
}
