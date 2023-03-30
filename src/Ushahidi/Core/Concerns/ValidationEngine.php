<?php

/**
 * Ushahidi ValidationEngine Trait
 *
 * Gives objects a method for storing an instance of a Validation class
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Core
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Concerns;

use Ushahidi\Core\Contracts\ValidationEngine as ValidationEngineContract;

trait ValidationEngine
{
    /**
     * @var \Ushahidi\Core\Contracts\ValidationEngine
     */
    protected $validation_engine;

    /**
     * @param  $validation_factory
     * @return void
     */
    public function setValidation(ValidationEngineContract $validation_engine)
    {
        $this->validation_engine = $validation_engine;
    }
}
