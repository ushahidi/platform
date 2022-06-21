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


namespace v5\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Ushahidi\Core\Entity\Permission;

class PostValueAllowed implements Scope
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
        $authorizer = service('authorizer.post');
        $user = $authorizer->getUser();

        $postPermissions = new \Ushahidi\Core\Tool\Permissions\PostPermissions();
        $postPermissions->setAcl($authorizer->acl);
        /**
         * post value's response_private field
         */
        $excludePrivateValues = !$postPermissions->canUserReadPrivateValues(
            $user
        );
        /**
         * $model->table here refers to the post_value type table. For instance
         * post_int or post_geometry , this is needed because we save most values in
         * different tables with the same structure :|
         */
        $builder
            ->join('form_attributes', $model->table.'.form_attribute_id', '=', 'form_attributes.id')
            ->join('form_stages', 'form_attributes.form_stage_id', 'form_stages.id')
            ->join('forms', 'form_stages.form_id', 'forms.id')
        ;

        if ($excludePrivateValues) {
            $builder->where('form_attributes.response_private', '=', 0);
        }

        // if (
        //     $model->getTable() === 'post_datetime' &&
        //     !$postPermissions->acl->hasPermission($user, Permission::MANAGE_POSTS)
        // ) {
        //     $builder->where('forms.hide_time', '=', 0);
        // }

        $formAuthorizer = service('authorizer.form');
        $formPermissions = new \Ushahidi\Core\Tool\Permissions\FormPermissions();
        $formPermissions->setAcl($formAuthorizer->acl);
        /**
         * With scopes and the $builder, we check for basic permissions right on our initial
         * queries rather than process them after the fact.
         * Are you wondering "why do we send a null form_id to canUserEditForm?"
         * well dear reader that's because that method doesn't use a $form id at all
         * but *it likes to pretend it does* and I don't want to refactor /v3 today.
         */
        if (!$formPermissions->canUserEditForm($user, null)) {
            $builder->where('form_stages.show_when_published', '=', '1');
            $builder->where('form_stages.task_is_internal_only', '=', '0');
        }
    }
}
