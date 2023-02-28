<?php

/**
 * Ushahidi Config Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ushahidi\Core\Usecase\CreateUsecase;
use Illuminate\Contracts\Events\Dispatcher;
use App\Console\Commands\Concerns\ConsoleFormatter;
use Ushahidi\Core\Tool\Authorizer\ConsoleAuthorizer;
use Ushahidi\Core\Entity\ApiKeyRepository as EntityApiKeyRepository;

class ApikeySetCommand extends Command
{
    use ConsoleFormatter;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'apikey:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set apikey';

    /**
     * @var \Ushahidi\Contracts\Usecase
     */
    protected $usecase;

    // Execution router takes the action argument and uses it to reroute execution.
    public function handle(
        CreateUsecase $createUsecase,
        EntityApiKeyRepository $apiRepo,
        ConsoleAuthorizer $consoleAuth,
        Dispatcher $dispatcher
    ) {
        $createUsecase->setDispatcher($dispatcher);

        $response = $createUsecase
            ->setRepository($apiRepo)
            ->setAuthorizer($consoleAuth)
            ->setFormatter($this->getFormatter())
            ->interact();

        // Format the response and output
        $this->handleResponse($response);
    }

    /**
     * Override response handler to flatten array
     */
    protected function handleResponse($response)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($response));
        $result = [];
        foreach ($iterator as $leafValue) {
            $keys = [];
            foreach (range(0, $iterator->getDepth()) as $depth) {
                $keys[] = $iterator->getSubIterator($depth)->key();
            }
            $result[implode('.', $keys)] = $leafValue;
        }

        // Format as table
        $this->table(array_keys($result), [$result]);
    }
}
