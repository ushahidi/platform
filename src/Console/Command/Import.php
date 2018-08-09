<?php

/**
 * Ushahidi Import Console Commands
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Command;

use Illuminate\Console\Command;

use SplFileObject;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Psr\Log\LoggerInterface;

use League\Csv\Reader;
use Ushahidi\Core\Tool\MappingTransformer;
use Ushahidi\Core\Usecase\ImportUsecase;

class Import extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'import';

    /**
     * The console command signature.
     *
     * import source.csv mapping.json fixedvalues.json
     *
     * @var string
     */
    protected $signature = 'import {file} {map} {value} {--type=csv} {--limit=} {--offset=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import posts';

    public function __construct()
    {
        parent::__construct();

        $this->readerMap = [
            'csv' => service('filereader.csv')
        ];
        $this->transformer = service('transformer.mapping');
    }

    protected function getUsecase()
    {
        if (!$this->usecase) {
            // @todo inject
            $this->usecase = service('factory.usecase')
                ->get('posts', 'import')
                // Override authorizer for console
                ->setAuthorizer(service('authorizer.console'));
        }

        return $this->usecase;
    }


    /**
     * Map of readers
     * @var [FileReader, ...]
     */
    protected $readerMap = [];

    /**
     * @var Ushahidi\Core\Tool\MappingTransformer
     */
    protected $transformer;

    /**
     * @var Ushahidi\Core\Usecase\ImportUsecase
     * @todo  support multiple entity types
     */
    protected $usecase;

    protected function getReader($type)
    {
        return $this->readerMap[$type]();
    }

    public function handle()
    {
        // Get the filename
        $filename = $this->option('file');

        // Load mapping and pass to transformer
        $map = file_get_contents($this->argument('map'));
        $this->transformer->setMap(json_decode($map, true));

        // Load fixed values and pass to transformer
        $values = file_get_contents($this->argument('values'));
        $this->transformer->setFixedValues(json_decode($values, true));

        // Get CSV reader
        $reader = $this->getReader($this->option('type'));

        // Set limit..
        if ($limit = $this->option('limit')) {
            $reader->setLimit($limit);
        }
        // .. and offset
        if ($offset = $this->option('offset')) {
            $reader->setOffset($offset);
        }

        // Get the traversable results
        $payload = $reader->process($filename);

        // Get the usecase and pass in authorizer, payload and transformer
        $this->getUsecase()
            ->setPayload($payload)
            ->setTransformer($this->transformer);

        // Execute the import
        return $this->getUsecase()->interact();
    }
}
