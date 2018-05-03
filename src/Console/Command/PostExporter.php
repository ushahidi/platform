<?php

/**
 * Ushahidi Webhook Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Command;

use Illuminate\Console\Command;

use Ushahidi\Core\Entity\PostExportRepository;
use Ushahidi\Core\Entity\ExportJobRepository;
use \Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\ValidatorTrait;
use Ushahidi\Factory\DataFactory;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Traits\UserContext;

use Ushahidi\Core\Tool\Filesystem;
use Ushahidi\Core\Tool\FileData;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PostExporter extends Command
{

	use ValidatorTrait;
	use FormatterTrait;
	use AuthorizerTrait;
	private $usecase;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'export';

	/**
	 * The console command signature.
	 *
	 * @var string
	 */
	protected $signature = 'export {job} {--limit=100} {--offset=0} {--include-header=1}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export posts';

	protected function getUsecase()
	{
		if (!$this->usecase) {
			// @todo inject
			$this->usecase = service('factory.usecase')
				->get('posts', 'export');
		}

		return $this->usecase;
	}

	public function handle()
	{
		// @todo inject
		$this->formatter = service('formatter.entity.post.csv');

		// set CLI params to be the payload for the usecase
		$payload = [
			'job_id' => $this->argument('job'),
			'limit' => $this->option('limit'),
			'offset' => $this->option('offset'),
			'add_header' => $this->option('include-header'),
			'exporter' => true
		];
		// Get the usecase and pass in authorizer, payload and transformer
		$file  = $this->getUsecase()
			->setPayload($payload)
			->setFormatter($this->formatter)
			->interact();
		$this->line(json_encode($file));
	}

}
