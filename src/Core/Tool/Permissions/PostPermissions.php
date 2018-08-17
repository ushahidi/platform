<?php

/**
 * Post Permissions
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Permissions;

use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\Permission;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Tool\Permissions\AclTrait;
use Ushahidi\Core\Traits\AdminAccess;

class PostPermissions
{
    use AclTrait;
    use AdminAccess;

    /**
     * Does the current user have permission to see if a post if locked?
     *
     * @param  User   $user
     * @param  Post   $post
     * @return boolean
     */
    public function canUserSeePostLock(User $user, Post $post)
    {
        // At present only logged in users with Manage Post Permission can see that a Post is locked
        // @todo if we're checking manage posts, check that - don't check if a user can edit a form!?
        //       More accurately I think if they can edit a post, the should be able to see locks
        return $this->acl->hasPermission($user, Permission::MANAGE_POSTS);
    }

    /**
     * Does the user have permission to view this posts author
     *
     * @param  User           $user
     * @param  Post           $post
     * @param  FormRepository $form_repo
     * @return boolean
     */
    public function canUserSeeAuthor(User $user, Post $post, FormRepository $form_repo)
    {
        // If the user has manage post permission
        // @todo delegate to authorizer
        if ($this->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            // If so, they can also see post authors
            return true;
        }

        // If the post is structured
        if ($post->form_id) {
            // @todo inject form repo via constructor or take form as parameter
            // ... if not, check if the form has author set as hidden or public
            return !$form_repo->isTypeHidden($post->form_id, 'hide_author');
        }

        // Default to scrubbing author info for non-admins on unstructured posts
        return false;
    }

    /**
     * Does the user have permission to view this posts exact time
     *
     * @param  User           $user
     * @param  Post           $post
     * @param  FormRepository $form_repo
     * @return boolean
     */
    public function canUserSeeTime(User $user, Post $post, FormRepository $form_repo)
    {
        // If the user has manage post permission
        // @todo delegate to authorizer
        if ($this->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            // If so, they can also see post dates
            return true;
        }

        // If the post is structured
        if ($post->form_id) {
            // @todo inject form repo via constructor or take form as parameter
            // ... if not, check if the form has author set as hidden or public
            return !$form_repo->isTypeHidden($post->form_id, 'hide_time');
        }

        // Default to scrubbing info for non-admins on unstructured posts
        return false;
    }

    /**
     * Does the user have permission to view this posts exact location
     *
     * @param  User           $user
     * @param  Post           $post
     * @param  FormRepository $form_repo
     * @return boolean
     */
    public function canUserSeeLocation(User $user, Post $post, FormRepository $form_repo)
    {
        // If the user has manage post permission
        // @todo delegate to authorizer
        if ($this->acl->hasPermission($user, Permission::MANAGE_POSTS)) {
            // If so, they can also see post location
            return true;
        }

        // If the post is structured
        if ($post->form_id) {
            // @todo inject form repo via constructor or take form as parameter
            // ... if not, check if the form has location set as hidden or public
            return !$form_repo->isTypeHidden($post->form_id, 'hide_location');
        }

        // Default to scrubbing info for non-admins on unstructured posts
        return false;
    }

    /**
     * Test whether the user can manage posts
     *
     * @param  Post $post
     * @return Boolean
     */
    public function canUserManagePosts(User $user)
    {
        // Delegate to post authorizer
        return $this->acl->hasPermission($user, Permission::MANAGE_POSTS);
    }

    /**
     * Test whether the post instance requires value restriction
     *
     * @param  Post $post
     * @return Boolean
     */
    public function canUserReadPrivateValues(User $user)
    {
        // Delegate to post authorizer
        return $this->acl->hasPermission($user, Permission::MANAGE_POSTS);
    }

    /**
     * Does user have permission to see unpublished (draft/archived) posts
     *
     * @param  User   $user
     * @return boolean
     */
    public function canUserViewUnpublishedPosts(User $user)
    {
        return $this->acl->hasPermission($user, Permission::MANAGE_POSTS);
    }
}
