<?php

namespace Ushahidi\App\PlatformVerifier;

use Ushahidi\App\Tools\OutputText;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Illuminate\Support\Facades\DB as DB;

use Ushahidi\App\Facades\Features;

class Database
{
    private static $errors = [
        // SQLSTATE[HY000] [2002] Connection refused
        '2002' => 'Check that your MySQL server is installed and running, ' .
                    'and that the right DB_HOST and DB_PORT are set up in the .env file',
        '1049' => 'Check that the database in the .env file variable DB_DATABASE exists' .
                    'and that the database user in DB_USERNAME has permissions to access it.',
        '1045' => 'Check that DB_USERNAME, DB_PASSWORD and DB_USERNAME are correct in the .env file. ' .
                    'Verify that accessing mysql through a CLI with `mysql -u YOUR_DB_USERNAME -p ' .
                    'YOUR_DB_NAME and entering the password on prompt results in a successful connection to mysql.'
    ];

    public function verifyRequirements(bool $console = true, \Illuminate\Database\MySqlConnection $connection = null)
    {
        /*
        * Enable calling this with a mocked connection from unit tests, or using the regular class.
        * We can't inject it always because not everything that calls this has access to Illuminate\Support\Facades\DB
        */
        if (!$connection) {
            $connectTo = getenv('MULTISITE_DOMAIN') ? 'multisite' : 'mysql';
            $connection = \Illuminate\Support\Facades\DB::connection($connectTo);
        }
        try {
            $connection = $connection->getPdo();
            return Respond::successResponse('We were able to connect to the DB. Well done!', '', $console);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $explainer = isset(self::$errors[$code]) ? self::$errors[$code] : '';
            $errors = [['message' => $e->getMessage(), 'explainer' => $explainer]];
            return Respond::errorResponse($errors, $console);
        }
    }
}
