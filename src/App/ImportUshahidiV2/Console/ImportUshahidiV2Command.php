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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Bus\Dispatcher;

use Ushahidi\App\ImportUshahidiV2;
use Ushahidi\App\Multisite\OhanzeeResolver;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Tool\ManifestLoader;

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
                            {--H|host= : The database host to connect to}
                            {--P|port= : The password to connect to the DB}
                            {--f|force : Proceed even if there are posts in the V3+ DB}
                            {--X|params= : A file with extra parameters for the import job}
                            {--no-xact : Don\'t wrap the whole operation in a transaction (incompatible with rollback)}
                            {--rollback : Rollback import when finished (useful for testing)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Ushahidi V2 database';

    protected $dispatcher;

    protected $extraParams;

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
        // Check options
        if ($this->option('rollback') && $this->option('no-xact')) {
            $this->error('Options "rollback" and "no-xact" are not compatible');
            return 1;
        }

        // Check if we have a file with extra parameters for the import job
        if ($this->option('params')) {
            $loader = new ManifestLoader();
            $this->extraParams = $loader->loadManifestFromFile($this->option('params'));
            $this->info("Loaded extended parameters:");
            $this->info("  With mappings?: {$this->extraParams->hasMappings()}");
        } else {
            $this->extraParams = new ImportUshahidiV2\ManifestSchemas\ImportParameters();
        }

        // Check we don't already have v3 data
        if ($postRepo->getTotal() > 1 && !$this->option('force')) {
            $this->error('Deployment is not empty. Please import into an empty deployment');
            return 1;
        }

        // Build config
        $dbConfig = $this->getDbConfig();

        // Connect to DB and check connection
        $this->verifyCanConnectToDb($dbConfig);

        // Begin transaction
        if (!$this->option('no-xact')) {
            DB::beginTransaction();
            $resolver->connection()->begin();
        }

        // Create import record
        $import = new ImportUshahidiV2\Import();
        $importId = $importRepo->create($import);
        // + extract and save needed settings
        $this->info('Initializing import');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\SetupImport($importId, $dbConfig, $this->extraParams));
        
        // Collect all tables data
        $this->info('Copying raw data');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\CopyRawTables($importId, $dbConfig));

        // Create default survey
        // $this->info('Create default survey');
        // $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\CreateDefaultSurvey($importId, $dbConfig));

        // Import categories
        $this->info('Importing categories');
        $this->dispatcher->dispatchNow(
            new ImportUshahidiV2\Jobs\ImportCategories($importId, $dbConfig, $this->extraParams)
        );

        // Import forms
        $this->info('Import forms to surveys');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\ImportForms($importId, $dbConfig, $this->extraParams));

        // Import users
        $this->info('Importing users');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\ImportUsers($importId, $dbConfig));

        // Import incidents to posts
        $this->info('Importing incidents to posts');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\ImportIncidents($importId, $dbConfig));

        $this->info('Importing reporters to contacts');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\ImportReporters($importId, $dbConfig));

        $this->info('Importing messages');
        $this->dispatcher->dispatchNow(new ImportUshahidiV2\Jobs\ImportMessages($importId, $dbConfig));

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
        } elseif (!$this->option('no-xact')) {
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
        if ($this->option('port')) {
            $config['port'] = $this->option('port');
        }

        $defaults = config('database.connections.mysql');

        $v2_config = $config + $defaults ;
        // Deal with read / write settings if provided
        if (array_key_exists('read', $v2_config)) {
            unset($v2_config['read']);
        }
        if (array_key_exists('write', $v2_config)) {
            unset($v2_config['write']);
        }

        return $v2_config;
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
