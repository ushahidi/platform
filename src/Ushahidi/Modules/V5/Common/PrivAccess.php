<?php
/**
 * Ushahidi Privilege Access Trait
 *
 * Gives objects methods determining what privileges a model has.
 *
 */

namespace Ushahidi\Modules\V5\Common;

use Illuminate\Database\Eloquent\Model;

trait PrivAccess
{
    /**
     * Get a list of all possible privilges.
     * By default, returns standard HTTP REST methods.
     * @return Array
     */
    protected function getAllPrivs()
    {
        return ['read', 'create', 'update', 'delete', 'search'];
    }

    // Authorizer
    public function getAllowedPrivs(Model $model)
    {
        $privs = $this->getAllPrivs();
        $allowed = [];

        foreach ($privs as $priv) {
            if ($this->isAllowed($model, $priv)) {
                $allowed[] = $priv;
            }
        }

        return $allowed;
    }

    // Authorizer
    abstract public function isAllowed(Model $model, $privilege);
}
