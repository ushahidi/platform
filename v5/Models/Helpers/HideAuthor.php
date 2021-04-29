<?php
namespace v5\Models\Helpers;

class HideAuthor
{
    /**
     * @param $value the author value
     * @param $hide_author indicates if the form config requires hiding the author
     * and in the case of unstructured posts, indicates TRUE since unstructured posts have
     * the requirement of a user with MANAGE_POSTS permissions to see it
     * or to be their own post
     * @param $post_user_id indicates the post's ID to enable showing the author to the post's original author
     * @return null|string
     */
    public static function hideAuthor($value, $hide_author = true, $post_user_id = null)
    {
        if (!$value) {
            return null;
        }

        $authorizer = service('authorizer.post');
        $user = $authorizer->getUser();

        $postPermissions = new \Ushahidi\Core\Tool\Permissions\PostPermissions();
        $postPermissions->setAcl($authorizer->acl);
        /**
         * if the user cannot read private values then they also can't see author
         */
        $excludePrivateValues = !$postPermissions->canUserReadPrivateValues(
            $user
        );

        if (!$hide_author) {
            return $value;
        }

        if ($post_user_id && $user->getId() && $post_user_id === $user->getId()) {
            return $value;
        }

        if (!$excludePrivateValues) {
            return $value;
        }

        return null;
    }
}
