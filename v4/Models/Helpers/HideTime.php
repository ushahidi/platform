<?php
namespace v4\Models\Helpers;

class HideTime
{
    public static function hideTime($value)
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
        if (!$excludePrivateValues) {
            return $value;
        }
        $d = new \DateTime($value);
        return $d->setTime(0, 0, 0)->format('Y-m-d H:i:s');
    }
}
