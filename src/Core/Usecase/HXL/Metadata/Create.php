<?php
/**
 * Created by PhpStorm.
 * User: rowasc
 * Date: 5/16/18
 * Time: 11:55 PM
 */

namespace Ushahidi\Core\Usecase\HXL\Metadata;

use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Tool\ValidatorTrait;
use Ushahidi\Core\Usecase\CreateUsecase;

class Create extends CreateUsecase
{

    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        ValidatorTrait;

    protected function getEntity()
    {
        $entity = parent::getEntity();
        // Add user id if this is not provided
        if (empty($entity->user_id) && $this->auth->getUserId()) {
            $entity->setState(['user_id' => $this->auth->getUserId()]);
        }
        return $entity;
    }
}
