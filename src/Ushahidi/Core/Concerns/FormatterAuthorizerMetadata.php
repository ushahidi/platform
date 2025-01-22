<?php

/**
 * Ushahidi Formatter + Authorizer Trait
 *
 * Injects "allowed_privileges" into formatted data using an Authorizer.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Concerns;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Authorizer;

trait FormatterAuthorizerMetadata
{
    protected $auth;

    public function setAuth(Authorizer $auth)
    {
        $this->auth = $auth;
        return $this;
    }

    protected function getAllowedPrivs(Entity $entity)
    {
        if (!$this->auth) {
            throw new \LogicException('Authorizer must be defined by calling setAuth');
        }
        // interally, methods are referred to as privileges
        return $this->auth->getAllowedPrivs($entity);
    }

    protected function addMetadata(array $data, Entity $entity)
    {
        return $data + [
            'allowed_privileges' => $this->getAllowedPrivs($entity),
        ];
    }
}
