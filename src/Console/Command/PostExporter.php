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

use Ushahidi\Core\Tool\Filesystem;

class PostExporter extends Command
{

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
                ->get('posts_export', 'export')
                ->setAuthorizer(service('authorizer.export_job'))
                ->setFormatter(service('formatter.entity.post.csv'));
        }
        return $this->usecase;
    }

    public function handle()
    {
        // set CLI params to be the payload for the usecase
        $filters = [
            'limit' => $this->option('limit'),
            'offset' => $this->option('offset'),
            'add_header' => $this->option('include-header'),
        ];

        // Get the usecase and pass in authorizer, payload and transformer
        $file = $this->getUsecase()
            ->setFilters($filters)
            ->setIdentifiers(['job_id' => $this->argument('job')])
            ->interact();
        $this->line("Export generated in file: {$file['results'][0]['file']}");
    }
}
