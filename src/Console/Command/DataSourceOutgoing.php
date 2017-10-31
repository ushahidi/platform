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
		$limit = $this->option('limit');

		$totals = [];
		foreach ($sources as $id => $source) {
			$totals[] = [
				'Source'   => $source->getName(),
				'Total'    => $this->sources->processPendingMessages($limit, $id)
			];
		}

		return $this->table(['Source', 'Total'], $totals);
	}

}
