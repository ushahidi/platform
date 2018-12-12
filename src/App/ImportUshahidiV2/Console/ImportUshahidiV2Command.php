<?php

/**
 * Ushahidi Import Console Commands
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\ImportUshahidiV2\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Bus\Dispatcher;
use Ushahidi\App\ImportUshahidiV2;
use Ushahidi\App\Multisite\OhanzeeResolver;
use Ushahidi\Core\Entity\PostRepository;

class ImportUshahidiV2Command extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'import:ushahidiv2';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'import:ushahidiv2
                            {database : The name of the database to import}
                            {--u|user= : The username to connect to the DB}
                            {--p|password= : The password to connect to the DB}
                            {--host= : The database host to connect to}
                            {--rollback : Rollback import when finished (useful for testing)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Ushahidi V2 database';

    protected $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        parent::__construct();

        $this->dispatcher = $dispatcher;
    }

    public function handle(
        ImportUshahidiV2\Contracts\ImportRepository $importRepo,
        PostRepository $postRepo,
        OhanzeeResolver $resolver
    ) {
        // Check we don't already have v3 data
        if ($postRepo->getTotal() > 1) {
            $this->error('Deployment is not empty. Please import into an empty deployment');
            return 1;
        }

        // Build config
        $dbConfig = $this->getDbConfig();

        // Connect to DB and check connection
        $this->verifyCanConnectToDb($dbConfig);

        // Begin transaction
        DB::beginTransaction();
        $resolver->connection()->begin();

        // Create import record
        $import = new ImportUshahidiV2\Import();
        $importId = $importRepo->create($import);

        // Collect all table names
        // Copy all data to current DB with v2_ prefix
        $this->info('Copying raw data');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\CopyRawTables($importId, $dbConfig));

        // Create default survey
        $this->info('Create default survey');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\CreateDefaultSurvey($importId, $dbConfig));

        // Import users
        $this->info('Importing users');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\ImportUsers($importId, $dbConfig));

        // Import categories
        $this->info('Importing categories');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\ImportCategories($importId, $dbConfig));

        $this->info('Importing messages');

        // Import incidents to posts
        $this->info('Importing incidents to posts');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\ImportIncidents($importId, $dbConfig));

        // Import xyz

        // Mark import complete?
        $importId = $importRepo->update(
            $import->markComplete()
        );
        $this->info('Import complete!');

        // Rollback import
        if ($this->option('rollback')) {
            $this->info('Rolling back');
            DB::rollback();
            $resolver->connection()->rollback();
        } else {
            DB::commit();
            $resolver->connection()->commit();
        }
    }

    protected function getDbConfig()
    {
        $config = [
            'database' => $this->argument('database')
        ];

        if ($this->option('host')) {
            $config['host'] = $this->option('host');
        }
        if ($this->option('user')) {
            $config['username'] = $this->option('user');
        }
        if ($this->option('password')) {
            $config['password'] = $this->option('password');
        }

        $defaults = config('database.connections.mysql');

        return $config + $defaults;
    }

    protected function verifyCanConnectToDb($dbConfig)
    {
        // Configure database
        config(['database.connections.importv2' => $dbConfig]);

        try {
            $users = DB::connection('importv2')->table('users')->count();
        } catch (\Illuminate\Database\QueryException $e) {
            throw new \RuntimeException('Could not connect to database', 0, $e);
        }
    }
}
