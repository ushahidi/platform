<?php

/**
 * Ushahidi Data Provider Console Commands
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataSource\Console;

use Illuminate\Console\Command;
use Ushahidi\DataSource\DataSourceManager;
use Ushahidi\DataSource\DataSourceStorage;
use Ushahidi\DataSource\Contracts\IncomingDataSource;

class IncomingCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datasource:incoming';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'datasource:incoming {--source=} {--all} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch incoming messages from data sources';

    /**
     * @var DataSourceManager
     */
    protected $sources;

    /**
     * @var DataSourceStorage
     */
    protected $storage;

    public function __construct(DataSourceManager $sources, DataSourceStorage $storage)
    {
        parent::__construct();
        $this->sources = $sources;
        $this->storage = $storage;
    }

    protected function getSources()
    {
        if ($source = $this->option('source')) {
            $sources = [$source];
        } elseif ($this->option('all')) {
            $sources = $this->sources->getSources();
        } else {
            $sources = $this->sources->getEnabledSources();
        }
        return $sources;
    }

    public function handle()
    {
        $sources = $this->getSources();
        $limit = $this->option('limit');

        $totals = [];

        foreach ($sources as $sourceId) {
            $source = $this->sources->getSource($sourceId);
            if (!($source instanceof IncomingDataSource)) {
                // Data source doesn't have an API we can pull messages from
                continue;
            }

            $messages = $source->fetch($limit);

            foreach ($messages as $index => $message) {
                try {
                    $this->storage->receive(
                        $sourceId,
                        $message['type'],
                        $message['contact_type'],
                        $message['from'],
                        $message['message'],
                        $message['to'],
                        $message['title'],
                        $message['datetime'],
                        $message['data_source_message_id'],
                        $message['additional_data'],
                        $source->getInboundFormId(),
                        $source->getInboundFieldMappings()
                    );
                } catch (\Throwable $th) {
                    report($th);
                    unset($messages[$index]);
                }
            }

            $totals[] = [
                'Source'   => $source->getName(),
                'Total'    => count($messages)
            ];
        }

        return $this->table(['Source', 'Total'], $totals);
    }
}
