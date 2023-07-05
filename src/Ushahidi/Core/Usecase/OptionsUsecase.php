<?php

/**
 * Ushahidi Platform Options Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

use Ushahidi\Contracts\Entity;
use Ushahidi\Contracts\Usecase;
use Ushahidi\Core\Usecase\Concerns\Formatter as FormatterTrait;
use Ushahidi\Core\Usecase\Concerns\Authorizer as AuthorizerTrait;
use Ushahidi\Core\Usecase\Concerns\Translator as TranslatorTrait;
use Ushahidi\Core\Concerns\IdentifyRecords;
use Ushahidi\Contracts\Repository\ReadRepository;

class OptionsUsecase implements Usecase
{
    // Uses several traits to assign tools. Each of these traits provides a
    // setter method for the tool. For example, the AuthorizerTrait provides
    // a `setAuthorizer` method which only accepts `Authorizer` instances.
    use AuthorizerTrait,
        FormatterTrait,
        TranslatorTrait;

    // - IdentifyRecords for setting entity lookup parameters
    use IdentifyRecords;

    /**
     * @var \Ushahidi\Contracts\Repository\ReadRepository
     */
    protected $repo;

    /**
     * Inject a repository that can read entities.
     *
     * @param  ReadRepository $repo
     * @return $this
     */
    public function setRepository(ReadRepository $repo)
    {
        $this->repo = $repo;
        return $this;
    }

    // Usecase
    public function isWrite()
    {
        return false;
    }

    // Usecase
    public function isSearch()
    {
        return false;
    }

    // Usecase
    public function interact()
    {
        // Fetch an empty entity...
        $entity = $this->getEntity();

        // ... grab the privileges that are allowed for the current user
        $data = [
            'allowed_privileges' => $this->getAllowedPrivs($entity)
        ];

        // ... and return the formatted results.
        return $data;
    }

    /**
     * Get an empty entity.
     *
     * @return Entity
     */
    protected function getEntity()
    {
        return $this->repo->getEntity();
    }
}
