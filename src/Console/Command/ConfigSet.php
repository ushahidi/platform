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

class ConfigSet extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'config:set';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'config:set {group} {value} {--key=} {--json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set config params';

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
                ->get('config', 'update')
                // Override authorizer for console
                ->setAuthorizer(service('authorizer.console'))
                // Override formatter for console
                ->setFormatter(service('formatter.entity.console'));
        }

        return $this->usecase;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $group = $this->argument('group');
        $key   = $this->option('key');
        $is_json   = $this->option('json');
        $value = $this->argument('value');

        if ($key) {
            $value = [
                $key => $is_json ? json_decode($value, true) : $value
            ];
        } else {
            $value = json_decode($value, true);
            if (!is_array($value)) {
                $value = [];
            }
        }

        $this->getUsecase()->setIdentifiers([ 'id' => $group ])
            ->setPayload($value);

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
