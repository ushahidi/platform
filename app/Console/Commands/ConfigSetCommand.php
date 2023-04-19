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
use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Core\Tool\Authorizer\ConsoleAuthorizer;
use Ushahidi\Core\Usecase\UpdateUsecase;
use App\Console\Commands\Concerns\ConsoleFormatter;

class ConfigSetCommand extends Command
{
    use ConsoleFormatter;

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
     * @var \Ushahidi\Contracts\Usecase
     * @todo  support multiple entity types
     */
    protected $usecase;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(
        UpdateUsecase $updateUsecase,
        ConfigRepository $configRepo,
        ConsoleAuthorizer $consoleAuth
    ) {
        $group = $this->argument('group');
        $key = $this->option('key');
        $is_json = $this->option('json');
        $value = $this->argument('value');

        if ($key) {
            $value = [
                $key => $is_json ? json_decode($value, true) : $value,
            ];
        } else {
            $value = json_decode($value, true);
            if (! is_array($value)) {
                $value = [];
            }
        }

        $updateUsecase->setIdentifiers(['id' => $group]);

        $updateUsecase->setPayload($value);

        $updateUsecase->setAuthorizer($consoleAuth);

        $updateUsecase->setRepository($configRepo);

        $updateUsecase->setFormatter($this->getFormatter());

        $response = $updateUsecase->interact();

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
