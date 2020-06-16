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


namespace v4\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        $excludePrivateValues = !$postPermissions->canUserReadPrivateValues(
            $user
        );

        $q = $builder
            ->join('form_attributes', $model->table.'.form_attribute_id', '=', 'form_attributes.id');

        if ($excludePrivateValues) {
            $q = $builder->where('form_attributes.response_private', '=', 0);
        }

        return $q;
    }
}
