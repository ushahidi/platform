<?php

/**
 * Ushahidi Data Source Console Commands
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\DataSource\Console;

use Illuminate\Console\Command;

use Ushahidi\Core\Usecase;
use \Ushahidi\Factory\UsecaseFactory;
use Ushahidi\App\DataSource\DataSourceManager;
use Ushahidi\App\DataSource\DataSourceStorage;
use Ushahidi\App\DataSource\OutgoingAPIDataSource;

class OutgoingCommand extends Command
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

    public function __construct(DataSourceManager $sources, DataSourceStorage $storage)
    {
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
        $sources = $this->getSources();

        $totals = [];

        foreach ($sources as $id => $source) {
            if (!($source instanceof OutgoingAPIDataSource)) {
                // Data source doesn't have an API we can push messages to
                continue;
            }

            $totals[] = [
                'Source'   => $source->getName(),
                'Total'    => $this->processSource($source, $id)
            ];
        }

        // If no source is specified
        if (!$this->option('source')) {
            $totals[] = [
                'Source' => 'Unassigned', // @todo split into named types
                'Total' => $this->processUnassignedSource()
            ];
        }

        return $this->table(['Source', 'Total'], $totals);
    }

    protected function processSource($source)
    {
        $count = 0;
        $messages = $this->storage->getPendingMessages($this->option('limit'), $source->getId());

        foreach ($messages as $message) {
            list($new_status, $tracking_id) = $source->send($message->contact, $message->message, $message->title);

            $this->storage->updateMessageStatus($message->id, $new_status, $tracking_id);

            $count ++;
        }

        return $count;
    }

    protected function processUnassignedSource()
    {
        $count = 0;

        foreach (['sms', 'twitter', 'email'] as $type) {
            $source = $this->sources->getSourceForType($type);

            // @todo what if the only outgoing source is SMSSync
            if (!($source instanceof OutgoingAPIDataSource)) {
                // Data source doesn't have an API we can push messages to
                continue;
            }

            $messages = $this->storage->getPendingMessagesByType($this->option('limit'), $type);

            foreach ($messages as $message) {
                list($new_status, $tracking_id) = $source->send($message->contact, $message->message, $message->title);

                $this->storage->updateMessageStatus($message->id, $new_status, $tracking_id);

                $count ++;
            }
        }

        return $count;
    }
}
