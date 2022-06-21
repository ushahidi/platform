<?php

namespace Ushahidi\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Collection;

use Ushahidi\Core\Entity\MediaRepository;
use Ushahidi\Core\SearchData;
use Ushahidi\App\Jobs\ImportMediaJob;

class ImportMediaCommand extends Command
{
    protected const DETACHED_JOB_CHUNK_SIZE = 10;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'import:media';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'import:media
                            {--d|detached : Run media import in the background with queue workers}
                            {--s|source= : Source storage driver to read files from}
                            {--r|regex= : Only import media addresses matching regex}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import referenced media to deployment\'s filesystem';
    
    protected $dispatcher;


    public function __construct(Dispatcher $dispatcher)
    {
        parent::__construct();

        $this->dispatcher = $dispatcher;
    }

    public function handle(MediaRepository $mediaRepo)
    {
        // Find all media rows
        $mediaRepo->setSearchParams(new SearchData());
        $results = $mediaRepo->getSearchResults();

        // Filter out results by regex
        if ($this->option('regex')) {
            // process the pattern
            $p = $this->option('regex');
            if ($p[0] !== '/') {
                $p = '/' . $p . '/';
            }
            $results = array_filter($results, function ($media) use ($p) {
                return ( preg_match($p, $media->o_filename) == 1 );
            });
        }

        // Launch import media jobs
        if (!$this->option('detached')) {
            foreach ($results as $media) {
                $this->info("Processing media with id {$media->id} and url {$media->o_filename}");
                $this->dispatcher->dispatchNow($this->makeJob([$media->id]));
            }
        } else {
            // Launch import job per chunk
            foreach (collect($results)->chunk(self::DETACHED_JOB_CHUNK_SIZE) as $chunk) {
                $chunk_ids = $chunk->map(function ($m) {
                    return $m->id;
                })->toArray();
                $this->info("Sending job to import media ids: " .
                    json_encode(array_values($chunk_ids)));
                $this->dispatcher->dispatch($this->makeJob($chunk_ids));
            }
        }
    }

    protected function makeJob(array $mediaIds)
    {
        if ($this->option('source')) {
            return new ImportMediaJob($mediaIds, $this->option('source'));
        } else {
            return new ImportMediaJob($mediaIds);
        }
    }
}
