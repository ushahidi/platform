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

use \Ushahidi\Factory\UsecaseFactory;
use Ushahidi\Core\Exception\ValidatorException;
use Ushahidi\Core\Entity\TosRepository;
use Ushahidi\Core\Entity\UserRepository;

class UserCreate extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'user:create';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'user:create {--realname=} {--email=} {--role=admin} {--password=} {--with-hash} {--tos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user';

    protected $validator;
    protected $repo;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->repo = service('repository.user');
        $this->validator = service('factory.validator')->get('users', 'create');
        $this->tosRepo = service('repository.tos');

        $state = [
            'realname' => $this->option('realname') ?: null,
            'email' => $this->option('email'),
            // Default to creating an admin user
            'role' => $this->option('role') ?: 'admin',
            'password' => $this->option('password'),
        ];

        if (!$this->validator->check($state)) {
            throw new ValidatorException('Failed to validate user', $this->validator->errors());
        }

        $entity = $this->repo->getEntity();
        $entity->setState($state);
        $id = $this->option('with-hash') ? $this->repo->createWithHash($entity) : $this->repo->create($entity);

        $acceptTos = $this->option('tos');
        if ($acceptTos) {
                $tos = $this->tosRepo->getEntity([
                        'user_id' => $id,
                        'tos_version_date' => getenv('TOS_RELEASE_DATE')
                        ? date_create(getenv('TOS_RELEASE_DATE'), new \DateTimeZone('UTC'))
                        : date_create()
                ]);

                $this->tosRepo->create($tos);
        }

        $this->info("Account was created successfully, id: {$id}");
    }
}
