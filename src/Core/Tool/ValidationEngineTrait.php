<?php

/**
 * Ushahidi ValidationEngineTrait
 *
 * Gives objects a method for storing an instance of a Validation class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Core
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Aura\Di\InstanceFactory;
use Ushahidi\Core\Tool\Validation;

trait ValidationEngineTrait
{
    /**
     * @var Ushahidi\Core\Tool\Validation
     */
    protected $validation_engine;

    /**
     * @param  $validation_factory
     * @return void
     */
    public function setValidation(InstanceFactory $validation_factory)
    {
        $this->validation_engine = $validation_factory();
    }
}
