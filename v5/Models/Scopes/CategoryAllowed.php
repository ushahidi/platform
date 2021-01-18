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
use v5\Models\Category;

class CategoryAllowed implements Scope
{

    /**
     * Scope helper to only pull tags we are allowed to get from the db
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        /**
         * If no roles are selected, the Tag is considered
         * completely public.
         */
        $authorizer = service('authorizer.tag');
        $user = $authorizer->getUser();
        if ($user->role) {
            // couldn't think of a better way to deal with our JSON-but-not-json fields
            // get categories that are available for users with this role or NULL role
            // taking care NOT to bring any child categories that belong
            // to parents with other role restrictions

            // generates a query like like this
            // select * from `tags` where (`role` is null or `role` LIKE ?)
            // AND (`parent_id` not in
            // (
            //  select `id` from `tags` where `role` NOT LIKE ? and `parent_id` is null
            // )
            // or `parent_id` is null)
            $builder->where(function ($query) use ($user) {
                return $query
                    ->whereNull('role')
                    ->orWhere('role', 'LIKE', '%\"' . $user->role . '\"%')
                    ;
            });
            $builder->where(function ($query) use ($user) {
                return $query
                    ->whereNotIn('parent_id', function ($query) use ($user) {
                        $query
                            ->select('id')
                            ->from('tags')
                            //@note what's a nicer way to bind this ????
                            ->where('role', 'NOT LIKE', '%\"' . $user->role . '\"%')
                            ->whereNull('parent_id');
                    })
                    ->orWhereNull('parent_id');
            });
        } else {
            // get categories that are available for non logged in users
            // taking care NOT to bring any child categories that belong
            // to parents with admin/user/other role restrictions
            $builder->whereNull('role')->where(function ($query) use ($user) {
                // generates a query like this:
                // select * from `tags` where `role` is null
                // AND (`parent_id` not in
                // (
                //  select `id` from `tags` where `role` is not null and `parent_id` is null
                // )
                // or `parent_id` is null)
                return $query
                    ->whereNotIn('parent_id', function ($query) use ($user) {
                        $query
                            ->select('id')
                            ->from('tags')
                            ->whereNotNull('role')
                            ->whereNull('parent_id');
                    })
                    ->orWhereNull('parent_id');
            });
        }
    }
}
