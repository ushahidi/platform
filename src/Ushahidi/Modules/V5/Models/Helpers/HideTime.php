<?php

namespace Ushahidi\Modules\V5\Models\Helpers;

class HideTime
{
    public static function hideTime($value, $hide_time)
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

        if (!$hide_time) {
            return self::createDateTime($value)->format(\DateTimeInterface::ISO8601);
        }

        if (!$excludePrivateValues) {
            return self::createDateTime($value)->format(\DateTimeInterface::ISO8601);
        }
        return self::createDateTime($value)->setTime(0, 0, 0)->format(\DateTimeInterface::ISO8601);
    }

    private static function createDateTime($value)
    {
        $d = new \DateTime();
        if (is_numeric($value)) {
            $d->setTimestamp($value);
        } else {
            $d = new \DateTime((string)$value);
        }
        return $d;
    }
}
