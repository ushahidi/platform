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
use \Ushahidi\Factory\UsecaseFactory;

class ConfigGet extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'config:get';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'config:get {group}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get config params';

    /**
     * @var Ushahidi\Core\Usecase\Usecase
     * @todo  support multiple entity types
     */
    protected $usecase;

    public function __construct()
    {
        parent::__construct();
    }

    protected function getUsecase()
    {
        if (!$this->usecase) {
            // @todo inject
            $this->usecase = service('factory.usecase')
                ->get('config', 'read')
                // Override authorizer for console
                ->setAuthorizer(service('authorizer.console'))
                // Override formatter for console
                ->setFormatter(service('formatter.entity.console'));
        }

        return $this->usecase;
    }

    public function handle()
    {
        $group = $this->argument('group');

        $this->getUsecase()->setIdentifiers([ 'id' => $group ]);

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
