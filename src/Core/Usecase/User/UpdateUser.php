<?php
namespace Ushahidi\Core\Usecase\User;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Mailer;
use Ushahidi\Core\Usecase\UpdateUsecase;

class UpdateUser extends UpdateUsecase
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * Inject a mailer
     *
     * @param  $mailer Mailer
     * @return $this
     */
    public function setMailer(Mailer $mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }

    // Usecase
    public function interact()
    {
        // Fetch the entity and apply the payload...
        $entity = $this->getEntity()->setState($this->payload);

        // ... verify that the entity can be updated by the current user
        $this->verifyUpdateAuth($entity);

        // ... verify that the entity is in a valid state
        $this->verifyValid($entity);

        // ... persist the changes
        $this->repo->update($entity);

        // ... notify user via email if password changed
        $this->notifyUserPasswordChanged($entity);

        // ... check that the entity can be read by the current user
        if ($this->auth->isAllowed($entity, 'read')) {
            // ... and either load the updated entity from the storage layer
            $updated_entity = $this->getEntity();

            // ... and return the updated, formatted entity
            return $this->formatter->__invoke($updated_entity);
        } else {
            // ... or just return nothing
            return;
        }
    }

    protected function notifyUserPasswordChanged(Entity $entity)
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
