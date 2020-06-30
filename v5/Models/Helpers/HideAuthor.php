<?php
namespace v4\Models\Helpers;

class HideAuthor
{
    public static function hideAuthor($value, $hide_author)
    {
        if (!$value) {
            return null;
        }

        $authorizer = service('authorizer.post');
        $user = $authorizer->getUser();

        $postPermissions = new \Ushahidi\Core\Tool\Permissions\PostPermissions();
        $postPermissions->setAcl($authorizer->acl);
        /**
         * if the user cannot read private values then they also can't see hide_time
         */
        $excludePrivateValues = !$postPermissions->canUserReadPrivateValues(
            $user
        );

        if (!$hide_author) {
            return $value;
        }

        if (!$excludePrivateValues) {
            return $value;
        }

        return null;
    }
}
