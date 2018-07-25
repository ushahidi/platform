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
    protected $signature = 'db:obfuscatedata {subdomain?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obfuscates selected PII and removes various config keys from database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //set up repos
        $this->contactRepository = service('repository.contact');
        $this->postRepository = service('repository.post');
        $this->configRepository = service('repository.config');
        $this->userRepository = service('repository.user');

        $this->db = service('kohana.db');

        if ($this->isThisAMultisiteInstall()) {
            if (!getenv('HOST') || strlen(getenv('HOST')) < 1) {
                $this->alert("ERROR: A host must be specified for a multisite deployment.");
                exit;
            }
        }

        if ($this->isTestEnvironment() || $this->isStagingEnvironment()) {
            //confirm acknowledgements
            $this->alert("WARNING: This script will wipe user, contacts, post author and data source data.");
            if (!$this->confirm("Do you want to continue?")) {
                $this->info("Request canceled.\n");
                exit;
            }
            if (!$this->confirm('Do you acknowledge that this data will still contain sensitive data?')) {
                $this->info("Request canceled.\n");
                exit;
            }

            // Do overwriting
            $this->db->begin();
            $this->overwriteContacts();
            $this->overwritePostAuthors();
            $this->removeDataProviderValues();
            $this->overwriteSiteConfig();
            $this->deleteUsers();
            $this->addAdminUser();
            $this->db->commit();
        } else {
            $this->info("This script will only run on test or staging deployments.\n\n");
            exit;
        }
    }

    protected function isThisAMultisiteInstall()
    {
        // @TODO: is this the correct way to check against multisite
        return (config('multisite.domain'));
    }

    private function isTestEnvironment()
    {
        $cur_env = $this->getLaravel()->environment();
        return (strtolower($cur_env) == 'test');
    }

    private function isStagingEnvironment()
    {
        $cur_env = $this->getLaravel()->environment();
        return (strtolower($cur_env) == 'staging');
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

    private function overwriteContacts()
    {
        $this->info("Overwriting contact data...");
        $faker = Faker\Factory::create();
        $query = DB::select('contacts.id', 'contacts.type', 'contacts.contact')
            ->distinct(true)
            ->from('contacts');
        $results = $query->execute($this->db);
        $overwrittenCount = 0;
        $resultsCount = count($results);
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
        if ($overwrittenCount == $resultsCount) {
            $this->info("updated ".$overwrittenCount." records.\n");
            return $overwrittenCount;
        } else {
            $this->info("failed to overwrite all records.\n");
            return false;
        }
    }

    private function overwritePostAuthors()
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
            $this->info("updated ".$overwrittenCount." records.\n");
            return $overwrittenCount;
        } else {
            $this->info("failed to overwrite all records.\n");
            return false;
        }
    }

    private function overwriteSiteConfig()
    {
        $this->info("\nRemoving general settings data...");
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
            //TODO: reset these?
            //'cient_url' => '??',
            // 'url' => '??'
        ]);
        $this->configRepository->update($siteConfig);
        $this->info("\nDone.");
    }

    private function removeDataProviderValues()
    {
        $this->info("\nORemoving dataProvider data...");
        $dataProviderConfig = $this->configRepository->get('data-provider');
        //TODO: walk through each key instead?
        //TODO:  otherwise set them to defaults?
        $dataProviderConfig->setState([
            'url' => '',
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
        $this->info("\nSet all dataproviders to false.");
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
            $this->info("updated ".$overwrittenCount." user records.\n");
            return $overwrittenCount;
        } else {
            $this->info("failed to overwrite all user records.\n");
            return false;
        }
    }

    private function deleteUsers()
    {
        $this->info("Deleting users...");
        $query = DB::delete('users');
        $count = $query->execute($this->db);
        $this->info("removed ".$count." records.\n");
        return $count;
    }

    private function addAdminUser()
    {
        if (!$this->confirm("Do you want to add an admin user?")) {
            $this->info("Request canceled.\n");
            return;
        } else {
            //TODO: add input validation
            $adminEmail = $this->anticipate('Email address for admin user?', ['admin@ushahidi.com']);
            $adminPassword = $this->secret('What should the password be?');
            $newUserEntity = $this->userRepository->getEntity();
            $newUserEntity->setState([
                'email' => $adminEmail,
                'realname' => 'Admin User',
                'role' => 'admin',
                'password' => $adminPassword ]); // NOTE: pw is hashed via create
            $id = $this->userRepository->create($newUserEntity);
            $this->info("Created admin user with Id: ".$id);
        }
    }
}
