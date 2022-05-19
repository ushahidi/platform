<?php

/**
 * Ushahidi Validator Tool Trait
 *
 * Gives objects a method for storing an validator instance.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Concerns;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Validator as ValidatorInterface;
use Ushahidi\Core\Exception\ValidatorException;

trait Validator
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param  ValidatorInterface $validator
     * @return self
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * Verify that the given entity is valid.
     *
     * @param  Entity $entity
     * @return void
     */
    abstract protected function verifyValid(Entity $entity);

    /**
     * Throw a ValidatorException
     *
     * @param  Entity $entity
     * @return null
     * @throws ValidatorException
     */
    protected function validatorError(Entity $entity)
    {
        throw new ValidatorException(sprintf(
            $this->translator->trans('validation.failedToValidate'),
            $entity->getResource()
        ), $this->validator->errors());
    }
}
