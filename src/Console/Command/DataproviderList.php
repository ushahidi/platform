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

class DataproviderList extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dataprovider:list';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'dataprovider:list {--provider=} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List data providers';

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

		$list = [];
		foreach ($providers as $id => $provider) {
			$list[] = [
				'Name'        => $provider->name,
				'Version'      => $provider->version,
				'Capabilities' => implode(', ', array_keys(array_filter($provider->services))),
			];
		}

		return $this->table(['Name', 'Version', 'Capabilities'], $list);
	}

}
