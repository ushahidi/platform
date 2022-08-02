<?php
/**
 * Created by PhpStorm.
 * User: rowasc
 * Date: 5/16/18
 * Time: 11:55 PM
 */

namespace Ushahidi\App\V3\Usecase\HXL\Metadata;

use Ushahidi\App\V3\Usecase\Concerns\Authorizer as AuthorizerTrait;
use Ushahidi\App\V3\Usecase\Concerns\Formatter as FormatterTrait;
use Ushahidi\App\V3\Usecase\Concerns\Validator as ValidatorTrait;
use Ushahidi\App\V3\Usecase\CreateUsecase;

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
