<?php

/**
 * Ushahidi User Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Command;

use Illuminate\Console\Command;

use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Exception\NotFoundException;

class UserDelete extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:delete';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'user:delete {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a user';

    protected $repo;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->repo = service('repository.user');

        $email = $this->option('email');

        $entity = $this->repo->getByEmail($email);

        if (!$entity->getId()) {
            throw new NotFoundException(sprintf(
                'Could not locate any %s matching [%s]',
                $entity->getResource(),
                $email
            ));
        }

        $id = $this->repo->delete($entity);

        $this->info("Account was deleted successfully, id: {$id}");
    }
}
