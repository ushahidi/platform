<?php
/**
 * Ushahidi Acl
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2020 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 *
 */


namespace Ushahidi\App\V5\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class StageAllowed implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $authorizer = service('authorizer.form');
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $user = $authorizer->getUser();

        $formPermissions = new \Ushahidi\Core\Tool\Permissions\FormPermissions();
        $formPermissions->setAcl($authorizer->acl);
        /**
         * With scopes and the $builder, we check for basic permissions right on our initial
         * queries rather than process them after the fact.
         * Are you wondering "why do we send a null form_id to canUserEditForm?"
         * well dear reader that's because that method doesn't use a $form id at all
         * but *it likes to pretend it does* and I don't want to refactor /v3 today.
         */
        if (!$formPermissions->canUserEditForm($user, null)) {
            $builder->where('show_when_published', '=', '1');
            $builder->where('task_is_internal_only', '=', '0');
        }
    }
}
