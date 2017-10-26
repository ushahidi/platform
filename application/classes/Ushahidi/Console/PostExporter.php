<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Webhook Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Console\Command;
use Ushahidi\Core\Entity\PostExportRepository;
use Ushahidi\Factory\DataFactory;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Tool\FormatterTrait;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class Ushahidi_Console_PostExporter extends Command
{

	use UserContext;
	use FormatterTrait;

    private $data;
	private $postExportRepository;

	public function setDataFactory(DataFactory $data)
	{
		$this->data = $data;
	}

	public function setPostExportRepo(PostExportRepository $repo)
	{
		$this->postExportRepository = $repo;
	}

	protected function configure()
	{
		$this
			->setName('exporter')
			->setDescription('Export Posts')
			->addArgument('action', InputArgument::OPTIONAL, 'list, export', 'list')
			->addOption('limit', ['l'], InputOption::VALUE_OPTIONAL, 'limit')
            ->addOption('offset', ['o'], InputOption::VALUE_OPTIONAL, 'offset')
			;
	}

	protected function executeList(InputInterface $input, OutputInterface $output)
	{
		return [
			[
				'Available actions' => 'export'
			]
		];
	}

	protected function executeExport(InputInterface $input, OutputInterface $output)
	{

        $data = $this->data->get('search');

		$limit = $input->getOption('limit', 100);
        $offset = $input->getOption('offset', 0);

		$format = 'csv';

        $filters = [
            'limit' => $limit,
            'offset' => $offset,
			'status' => 'all',
			'exporter' => true
        ];

        foreach ($filters as $key => $filter) {
            $data->$key = $filter;
        }

        $this->postExportRepository->setSearchParams($data);
        
		
        $posts = $this->postExportRepository->getSearchResults();

		// ... get the total count for the search
		$total = $this->postExportRepository->getSearchTotal();

		// // ... remove any entities that cannot be seen
		foreach ($posts as $idx => $post) {

			// Retrieved Attribute Labels for Entity's values
			$post = $this->postExportRepository->retrieveColumnNameData($post->asArray());

			$posts[$idx] = $post;
		}

        $res = service("formatter.entity.post.$format")->__invoke($posts);
		$response = [
			[
				'Message' => sprintf('%d posts were found', $total)
			]
		];



		$this->handleResponse($response, $output);
	}
}
