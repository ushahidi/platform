<?php

/**
 * Ushahidi User Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ushahidi\Core\Facade\Features;
use Illuminate\Support\Facades\Validator;
use Ushahidi\Core\Exception\ValidatorException;
use Ushahidi\Contracts\Repository\Entity\TosRepository;
use Ushahidi\Contracts\Repository\Entity\UserRepository;

class UserCreateCommand extends Command
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

    public function handle(UserRepository $userRepo, TosRepository $tosRepo)
    {
        $this->validator = service('factory.validator')->get('users', 'create');

        $state = [
            'realname' => $this->option('realname') ?: null,
            'email' => $this->option('email'),
            // Default to creating an admin user
            'role' => $this->option('role') ?: 'admin',
            'password' => $this->option('password'),
        ];

        $validator = Validator::make($state, [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:7|max:72',
            'realname' => 'max:150',
            'role' => ['exists:roles', function ($attribute, $value, $fail) use ($userRepo) {
                $limit = Features::getLimit('admin_users');
                if ($limit !== INF && $value == 'admin') {
                    $total = $userRepo->getTotalCount(['role' => 'admin']);

                    if ($total >= $limit) {
                        $fail(trans('user.adminUserLimitReached'));
                    }
                }
            }]
        ]);

        if (!$validator->failed()) {
            throw new ValidatorException('Failed to validate user', $validator->errors());
        }

        $entity = $userRepo->getEntity();
        $entity->setState($state);
        $id = $this->option('with-hash') ? $userRepo->createWithHash($entity) : $userRepo->create($entity);

        $acceptTos = $this->option('tos');
        if ($acceptTos) {
            $tos = $tosRepo->getEntity([
                'user_id' => $id,
                'tos_version_date' => getenv('TOS_RELEASE_DATE')
                    ? date_create(getenv('TOS_RELEASE_DATE'), new \DateTimeZone('UTC'))
                    : date_create(),
            ]);

            $tosRepo->create($tos);
        }

        $this->info("Account was created successfully, id: {$id}");
    }
}
