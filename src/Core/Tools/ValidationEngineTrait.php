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

namespace Ushahidi\Core\Tools;

use Ushahidi\Contracts\Validation as ValidationEngine;

trait ValidationEngineTrait
{
    /**
     * @var \Ushahidi\Contracts\Validation
     */
    protected $validation_engine;

    /**
     * @param  $validation_factory
     * @return void
     */
    public function setValidation(ValidationEngine $validation_engine)
    {
        $this->validation_engine = $validation_engine;
    }
}
