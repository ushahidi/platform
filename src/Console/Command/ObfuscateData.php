<?php namespace Ushahidi\Console\Command;

/**
 * Ushahidi Obfuscate Data console command
 *  - This overwrites specific data within the current database
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Illuminate\Console\Command;
use Ushahidi\Core\Entity\PostRepository;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\Core\Entity\ConfigRepository;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\App\Multisite;
use Ohanzee\DB;
use DB as LaravelDB;
use Faker;
use Database;

class ObfuscateData extends Command
{
    private $postRepository;
    private $contactRepository;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:obfuscatedata';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'db:obfuscatedata
        {--use-faker : Uses the slower Faker library to generate realistic data },
        {--non-interactive : Overrides prompts },
        {--admin-username= : Adds an admin user to users table with auto-generated password }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obfuscates selected PII and removes various config keys from database';

    /**
         * Allow environment patterns (matched using Str::is)
         * @var [string...]
         */
    protected $allowedEnvironments = ['local', 'staging*', 'test*'];

    public function __construct()
    {
        parent::__construct();
        $this->db = service('kohana.db');
        $this->contactRepository = service('repository.contact');
        $this->postRepository = service('repository.post');
        $this->configRepository = service('repository.config');
        $this->userRepository = service('repository.user');
    }

    public function handle()
    {
        //check for sanity of admin-username
        if ($this->option('admin-username') && strlen($this->option('admin-username')) < 5) {
            $this->alert("ERROR: usernames must be longer than 4 characters");
            return;
        }

        if ($this->isThisAMultisiteInstall()) {
            if (!getenv('HOST') || strlen(getenv('HOST')) < 1) {
                $this->alert("ERROR: A host must be specified for a multisite deployment.");
                return;
            }
        }

        if ($this->getLaravel()->environment($this->allowedEnvironments)) {
            //confirm acknowledgements
            $this->alert("WARNING: This script will wipe user, contacts, post author and data source data.");
            if (!$this->option('non-interactive')) {
                if (!$this->confirm("Do you want to continue?")) {
                    $this->info("Request canceled.");
                    return;
                }
                if (!$this->confirm('Do you acknowledge that this data will still contain sensitive data?')) {
                    $this->info("Request canceled.");
                    return;
                }
            }

            // Do overwriting
            $this->db->begin();
            if ($this->option('use-faker')) {
                $this->overwriteContactsWithFaker();
            } else {
                $this->overwriteContacts();
            }
            if ($this->option('use-faker')) {
                $this->overwritePostAuthorsWithFaker();
            } else {
                $this->overwritePostAuthors();
            }
            $this->removeDataProviderValues();
            $this->overwriteSiteConfig();
            $this->deleteUsers();
            $this->addAdminUser();
            $this->db->commit();
        } else {
            $this->info("This script will only run on test or staging deployments.");
            return;
        }
        $this->info('Data scrubbing complete.');
    }

    protected function isThisAMultisiteInstall()
    {
        // @TODO: is this the correct way to check against multisite
        return (config('multisite.enabled'));
    }

    /* optional implementations...
    private function randomizeLongitudeLatitude($boundaries)
    {
    }
    */

    /* TODO: would this be useful?
    private function scrambleLongitudeLatitude($distanceFromOrigin = null)
    {
    }
    */

    protected function overwriteContacts()
    {
        $this->info("Overwriting post author data...");
        // @codingStandardsIgnoreLine
        $randomEmailGenerator = LaravelDB::raw("CONCAT(lpad(conv(floor(rand()*pow(26,8)), 10, 36), 8, 0),'@',LEFT(UUID(), 8),'.example.com')");
        $randomPhoneGenerator = LaravelDB::raw("CONCAT('+', CAST(rand()*10000000000 as UNSIGNED) )");
        $randomTwitterGenerator = LaravelDB::raw("CONCAT('@',lpad(conv(floor(rand()*pow(26,12)), 10, 36), 12, 0))");
        $randomTextGenerator = LaravelDB::raw("lpad(conv(floor(rand()*pow(26,12)), 10, 36), 12, 0)");

        $resultEmailCount = LaravelDB::table('contacts')
            ->where('type', 'email')
            ->update(['contact' => $randomEmailGenerator]);

        $resultPhoneCount = LaravelDB::table('contacts')
            ->where('type', 'phone')
            ->update(['contact' => $randomPhoneGenerator]);

        $resultTwitterCount = LaravelDB::table('contacts')
            ->where('type', 'twitter')
            ->update(['contact' => $randomTwitterGenerator]);

        $resultOtherCount = LaravelDB::table('contacts')
            ->where('type', '!=', 'twitter')
            ->where('type', '!=', 'email')
            ->where('type', '!=', 'phone')
            ->update(['contact' => $randomTextGenerator]);

        $totalChangedCount = ($resultEmailCount + $resultPhoneCount + $resultTwitterCount + $resultOtherCount);

        $this->info("Updated ".$totalChangedCount." contact records.");
    }

    private function overwriteContactsWithFaker()
    {
        $this->info("Overwriting contact data...");
        $faker = Faker\Factory::create();
        $query = DB::select('contacts.id', 'contacts.type', 'contacts.contact')
            ->distinct(true)
            ->from('contacts');
        $results = $query->execute($this->db);
        $resultsCount = count($results);

        // iterates through each contact record, overwrites with fake data
        $overwrittenCount = 0;
        foreach ($results as $row) {
            $contactEntity = $this->contactRepository->getEntity($row);
            if ($row['type'] == 'phone') {
                $fakePhone = $faker->e164PhoneNumber;
                $contactEntity->setState(['contact'=> $fakePhone]);
            } elseif ($row['type'] == 'email') {
                $fakeEmail = $faker->safeEmail;
                $contactEntity->setState(['contact'=> $fakeEmail]);
            } elseif ($row['type'] == 'twitter') {
                $fakeTwitter = '@fake'.preg_replace("/[^A-Za-z0-9]/", '', $faker->realText(15));
                $contactEntity->setState(['contact'=> $fakeTwitter]);
            } else {
                $fakeText = preg_replace("/[^A-Za-z0-9]/", '', $faker->realText(20));
                $contactEntity->setState(['contact'=> $fakeText]);
            }
            $overwrittenCount += $this->contactRepository->update($contactEntity);
        }

        //checks if overwritten count matches results count
        if ($overwrittenCount == $resultsCount) {
            $this->info("Updated ".$overwrittenCount." records.");
            return $overwrittenCount;
        } else {
            $this->info("Failed to overwrite all records.");
            return false;
        }
    }

    protected function overwritePostAuthors()
    {
        $this->info("Overwriting post author data...");
        // @codingStandardsIgnoreLine
        $randomEmail = LaravelDB::raw("CONCAT(lpad(conv(floor(rand()*pow(26,8)), 10, 36), 8, 0),'@',LEFT(UUID(), 8),'.example.com')");
        // @codingStandardsIgnoreLine
        $randomName = LaravelDB::raw("CONCAT(lpad(conv(floor(rand()*pow(26,8)), 10, 36), 8, 0),' ',lpad(conv(floor(rand()*pow(26,8)), 10, 36), 8, 0))");
        $resultCount = LaravelDB::table('posts')
            ->whereNotNull('author_email')
            ->update(['author_email' => $randomEmail,
                        'author_realname' => $randomName]);

        $this->info("Updated ".print_r($resultCount, true)." records.");
    }

    private function overwritePostAuthorsWithFaker()
    {
        $this->info("Overwriting post author data...");
        $faker = Faker\Factory::create();
        $results = DB::select('posts.*')
            ->from('posts')
            ->execute($this->db)
            ->as_array();
        $overwrittenCount = 0;
        $resultsCount = count($results);
        foreach ($results as $row) {
            $postEntity = $this->postRepository->getEntity($row);
            $fakeEmail = $faker->safeEmail;
            $fakeName = $faker->firstName." ".$faker->lastName;
            $postEntity->setState([
                'author_realname'=> $fakeName,
                'author_email'=> $fakeEmail,
            ]);
            $overwrittenCount += $this->postRepository->update($postEntity);
        }
        if ($overwrittenCount == $resultsCount) {
            $this->info("updated ".$overwrittenCount." records.");
            return $overwrittenCount;
        } else {
            $this->info("failed to overwrite all records.");
            return false;
        }
    }

    private function overwriteSiteConfig()
    {
        $this->info("Removing general settings data...");
        $siteConfig = $this->configRepository->get('site');

        //prepend with OBFUSCATED if not already prepended
        $newSiteName = $siteConfig->name;
        if (substr($siteConfig->name, 0, 11) !== 'OBFUSCATED:') {
            $newSiteName = 'OBFUSCATED: '.$newSiteName;
        }
        $newSiteDescription = $siteConfig->description;
        if (substr($newSiteDescription, 0, 11) !== 'OBFUSCATED:') {
            $newSiteDescription = 'OBFUSCATED: '.$newSiteDescription;
        }

        $siteConfig->setState([
            'name' => $newSiteName,
            'description' => $newSiteDescription,
            'email' => 'admin@ushahidi.com',
        ]);
        $this->configRepository->update($siteConfig);
        $this->info("Done.");
    }

    private function removeDataProviderValues()
    {
        $this->info("Removing dataProvider data...");
        $dataProviderConfig = $this->configRepository->get('data-provider');
        //TODO: walk through each key instead?
        //TODO:  otherwise set them to defaults?
        $dataProviderConfig->setState([
            'providers' => [
                'smssync' => false,
                'email' => false,
                'twilio' => false,
                'nexmo' => false,
                'twitter' => false,
                'frontlinesms' => false
            ],
            'frontlinesms'=> [],
            'nexmo'=> [],
            'smssync'=> [],
            'twilio'=> [],
            'email'=> [],
        ]);
        $this->configRepository->update($dataProviderConfig);
        $this->info("Set all dataproviders to false.");
    }

    private function overwriteUsers()
    {
        $this->info("Overwriting users...");
        $faker = Faker\Factory::create();
        $results = DB::select('users.*')
            ->from('users')
            ->execute($this->db)
            ->as_array();
        $overwrittenCount = 0;
        $resultsCount = count($results);
        foreach ($results as $row) {
            $userEntity = $this->userRepository->getEntity($row);
            $fakeEmail = $faker->safeEmail;
            $fakeName = $faker->firstName." ".$faker->lastName;
            $userEntity->setState([
                'email'=> $fakeEmail,
                'realname'=> $fakeName,
                //randomized password
                //'password'=> preg_replace("/[^A-Za-z0-9]/", '', $faker->realText(20))
            ]);
            $overwrittenCount += $this->userRepository->update($userEntity);
        }
        if ($overwrittenCount == $resultsCount) {
            $this->info("Updated ".$overwrittenCount." user records.");
            return $overwrittenCount;
        } else {
            $this->info("Failed to overwrite all user records.");
            return false;
        }
    }

    private function deleteUsers()
    {
        $this->info("Deleting users...");
        $query = DB::delete('users');
        $count = $query->execute($this->db);
        $this->info("Removed ".$count." records.");
        return $count;
    }

    private function addAdminUser()
    {
        //in all cases, if the username is specified, we create an admin user and password
        if ($this->option('admin-username')) { // checking length in fire()
            $adminEmail = $this->option('admin-username');
            $adminPassword = str_random(25);
            $this->info("Generated password: ".$adminPassword);
            $id = $this->saveAdminUser($adminEmail, $adminPassword);
            $this->info("Created admin user ".$adminEmail." with Id: ".$id);
        }
        //otherwise, if not in 'non-interactive' mode, we prompt for info
        if (!$this->option('non-interactive')) {
            if (!$this->confirm("Do you want to add an admin user?")) {
                $this->info("Admin user skipped.");
                return;
            } else { // if we *do* want to create admin user
                $adminEmail = $this->anticipate('Email address for admin user?', ['admin@ushahidi.com']);
                $adminPassword = $this->secret('What should the password be?');
                $id = $this->saveAdminUser($adminEmail, $adminPassword);
                $this->info("Created admin user with Id: ".$id);
            }
        }
    }

    private function saveAdminUser($username, $password)
    {
        $newUserEntity = $this->userRepository->getEntity();
        $newUserEntity->setState([
            'email' => $username,
            'realname' => 'Admin User',
            'role' => 'admin',
            'password' => $password ]); // NOTE: pw is hashed via create
        return $this->userRepository->create($newUserEntity);
    }
}
