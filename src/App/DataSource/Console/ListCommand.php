<?php

/**
 * Ushahidi Data Provider Console Commands
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

    public function __construct(\Ushahidi\App\DataSource\DataSourceManager $sources)
    {
        parent::__construct();
        $this->sources = $sources;
    }

    protected function getSources()
    {
        if ($source = $this->option('source')) {
            $sources = array_filter([$source => $this->sources->getSource($source)]);
        } elseif ($this->option('all')) {
            $sources = $this->sources->getSource();
        } else {
            $sources = $this->sources->getEnabledSources();
        }
        return $sources;
    }

    public function handle()
    {
        $sources = $this->getSources();

        $list = [];
        foreach ($sources as $id => $source) {
            $list[] = [
                'Name'        => $source->getName(),
                'Services'    => implode(', ', $source->getServices()),
            ];
        }

        return $this->table(['Name', 'Services'], $list);
    }
}
