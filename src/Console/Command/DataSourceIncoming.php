<?php

/**
 * Ushahidi Data Provider Console Commands
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

class DataSourceIncoming extends Command
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

	public function __construct(\Ushahidi\App\DataSource\DataSourceManager $sources) {
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
		$limit = $this->option('limit');

		$totals = [];

		foreach ($sources as $source) {
			$totals[] = [
				'Source'   => $source->getName(),
				'Total'    => $source->fetch($limit),
			];
		}

		return $this->table(['Source', 'Total'], $totals);
	}

}
