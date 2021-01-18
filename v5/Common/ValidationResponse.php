<?php
/**
 * *
 *  * Ushahidi Acl
 *  *
 *  * @author     Ushahidi Team <team@ushahidi.com>
 *  * @package    Ushahidi\Application
 *  * @copyright  2020 Ushahidi
 *  * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 *
 *
 */

namespace v5\Common;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class ValidationResponse
{
    public $errors = null;
    public $success = false;

    /**
     * ValidationResponse constructor.
     * @param bool $success
     * @param MessageBag $errors
     */
    public function __construct(bool $success, MessageBag $errors = null)
    {
        $this->success = $success;
        $this->errors = $errors ? $errors->all() : null;
    }

    /**
     * To discuss: do we want something like this (+ priv members? )
     * @return array|null
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function success()
    {
        return $this->success;
    }

    public function fails()
    {
        return !$this->success();
    }
}
