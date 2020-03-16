<?php
namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Usecase\UpdateUsecase;

class UpdateUser extends UpdateUsecase
{
    protected function sendNotifications(Entity $entity)
    {
        if ($entity->hasChanged('password')) {
            // Email the update message
            $message = <<<TEXT
            This is a notification to let you know that your Ushahidi password has been changed for the deployment.
            If you believe that you received this notification in error or you did not make this change,
            please contact your deployment administrator first to confirm if this was an administrative change.
            If not, feel free to contact the Ushahidi Support team.
            Thank you,
            Ushahidi Support.
TEXT;
            $source = app('datasources')->getSource('outgoingemail');
            $source->send($entity->email, $message, 'Ushahidi Account Password Changed');
        }
    }
}
