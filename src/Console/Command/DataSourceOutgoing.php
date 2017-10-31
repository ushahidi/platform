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
    protected $signature = 'datasource:outgoing {--provider=} {--all} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send outgoing messages via data sources';

	public function __construct() {
		parent::__construct();
		$this->repo = service('repository.dataprovider');
	}

	protected function getProviders()
	{
		if ($provider = $this->option('provider')) {
			$providers = [$this->repo->get($provider)];
		} else {
			$providers = $this->repo->all(!$this->option('all'));
		}
		return $providers;
	}

	public function handle()
	{
		$providers = $this->getProviders();
		$limit = $this->option('limit');

		// Hack: always include email no matter what!
		if (!isset($providers['email'])) {
			$providers['email'] = $this->repo->get('email');
		}

		$totals = [];
		foreach ($providers as $id => $provider) {
			$totals[] = [
				'Provider' => $provider->name,
				'Total'    => \DataProvider::process_pending_messages($limit, $id)
			];
		}

		return $this->table(['Provider', 'Total'], $totals);
	}

}
