<?php

/**
 * Ushahidi Config Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Command;

use Illuminate\Console\Command;
use Ushahidi\Core\Usecase;
use Ushahidi\Factory\UsecaseFactory;

class ApikeySet extends Command
{

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
     * @var Ushahidi\Core\Usecase\Usecase
     * @todo  support multiple entity types
     */
    protected $usecase;

    protected function getUsecase()
    {
        if (!$this->usecase) {
            // @todo inject
            $this->usecase = service('factory.usecase')
                ->get('apikeys', 'create')
                // Override authorizer for console
                ->setAuthorizer(service('authorizer.console'))
                // Override formatter for console
                ->setFormatter(service('formatter.entity.console'));
        }

        return $this->usecase;
    }

    // Execution router takes the action argument and uses it to reroute execution.
    public function handle()
    {
        $response = $this->getUsecase()->interact();

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
            $result[ join('.', $keys) ] = $leafValue;
        }

        // Format as table
        $this->table(array_keys($result), [$result]);
    }
}
