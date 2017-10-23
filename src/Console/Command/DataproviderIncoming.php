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

class DataproviderIncoming extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dataprovider:incoming';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'dataprovider:incoming {--provider=} {--all} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch incoming messages from data providers';

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

	public function fire()
	{
		$providers = $this->getProviders();
		$limit = $this->option('limit');

		$totals = [];

		foreach ($providers as $provider) {
			$totals[] = [
				'Provider' => $provider->name,
				'Total'    => \DataProvider::factory($provider->id)->fetch($limit),
			];
		}

		return $this->table(['Provider', 'Total'], $totals);
	}

}
