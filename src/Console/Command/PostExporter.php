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
use Ushahidi\Factory\DataFactory;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\Core\Tool\FormatterTrait;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PostExporter extends Command
{

	use UserContext;
	use FormatterTrait;

    private $data;
	private $postExportRepository;

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
    protected $signature = 'export {--limit=100} {--offset=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export posts';

	public function __construct()
	{
		parent::__construct();
		$this->data = service('factory.data');
		$this->postExportRepository = service('repository.posts_export');
	}

	public function fire()
	{
        $data = $this->data->get('search');

		$limit = $this->option('limit');
        $offset = $this->option('offset');

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

		$this->info("{$total} posts were found");
	}
}
