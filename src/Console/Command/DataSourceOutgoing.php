<?php

/**
 * Ushahidi Data Source Console Commands
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
use Ushahidi\App\DataSource\DataSourceManager;
use Ushahidi\App\DataSource\DataSourceStorage;
use Ushahidi\App\DataSource\OutgoingAPIDataSource;

class DataSourceOutgoing extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datasource:outgoing';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'datasource:outgoing {--source=} {--all} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send outgoing messages via data sources';

    /**
     * @var DataSourceManager
     */
    protected $sources;

    /**
     * @var DataSourceStorage
     */
    protected $storage;

    public function __construct(DataSourceManager $sources, DataSourceStorage $storage) {
        parent::__construct();
        $this->sources = $sources;
        $this->storage = $storage;
    }

    protected function getSources()
    {
        if ($source = $this->option('source')) {
            $sources = array_filter([$source => $this->sources->getSource($source)]);
        } elseif ($this->option('all')) {
            $sources = $this->sources->getSource();
        } else {
            $sources = $this->sources->getEnabledSources();

            // Hack: always include email no matter what!
            if (!isset($sources['email'])) {
                $sources['email'] = $this->sources->getSource('email');
            }
        }
        return $sources;
    }

    public function handle()
    {
        $source = $this->option('source');
        $limit = $this->option('limit');

        $totals = [];

        $messages = $this->storage->getPendingMessages($limit, $this->option('source'));

        foreach ($messages as $message) {
            if ($message->data_provider) {
                $source = $this->sources->getEnabledSources($message->data_provider);
            } else {
                $source = $this->sources->getSourceForType($message->type);
            }

            if (!($source instanceof OutgoingAPIDataSource)) {
                // Data source doesn't have an API we can push messages to
                continue;
            }

            list($new_status, $tracking_id) = $source->send($message->contact, $message->message, $message->title);

            // @todo save which provide sent message
            $this->storage->updateMessageStatus($message->id, $new_status, $tracking_id);

            if (isset($totals[$source->getId()])) {
                $totals[$source->getId()]['Total']++;
            } else {
                $totals[$source->getId()] = [
                    'Source'   => $source->getName(),
                    'Total'    => 1
                ];
            }
        }

        return $this->table(['Source', 'Total'], $totals);
    }

}
