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

class ListCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'datasource:list';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'datasource:list {--source=} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List data sources';

    public function __construct(\Ushahidi\DataSource\DataSourceManager $sources)
    {
        parent::__construct();
        $this->sources = $sources;
    }

    protected function getSources()
    {
        if ($source = $this->option('source')) {
            return [$source];
        }
        
        if ($this->option('all')) {
            return $this->sources->getSources();
        }

        return $this->sources->getEnabledSources();
    }

    public function handle()
    {
        $sources = $this->getSources();

        $list = [];
        foreach ($sources as $id) {
            $source = $this->sources->getSource($id);
            $list[] = [
                'Name'        => $source->getName(),
                'Services'    => implode(', ', $source->getServices()),
            ];
        }

        return $this->table(['Name', 'Services'], $list);
    }
}
