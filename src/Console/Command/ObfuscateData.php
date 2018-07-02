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
use Ohanzee\DB;
use Faker;

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
    protected $signature = 'db:obfuscatedata';

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
        $this->db = service('kohana.db');
        $this->contactRepository = service('repository.contact');
        $this->postRepository = service('repository.post');
        $this->configRepository = service('repository.config');
        $this->userRepository = service('repository.user');

        if ($this->isTestDeployment() || $this->isStagingDeployment()) {
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

    private function isTestDeployment()
    {
        return (strtolower(getenv('APP_ENV')) == 'test');
    }

    private function isStagingDeployment()
    {
        return (strtolower(getenv('APP_ENV')) == 'staging');
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
                $fakeTwitter = '@'.preg_replace('/\s+/', '_', $faker->realText());
                $contactEntity->setState(['contact'=> $fakeTwitter]);
            } else {
                $fakeText = preg_replace('/\s+/', '_', $faker->realText());
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
        $this->info("\nORemoving general settings data...");
        $siteConfig = $this->configRepository->get('site');
        $siteConfig->setState([
            'name' => 'Test site',
            'description' => 'Staging deployment for testing',
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

    private function deleteUsers()
    {
        $this->info("Deleting users...");
        $query = DB::delete('users');
        $count = $query->execute($this->db);
        $this->info("removed ".$count." records.\n");
        return $count;
    }

    //TODO: unhardcode this if this is a feature we want
    private function addAdminUser()
    {
        $newUserEntity = $this->userRepository->getEntity();
        $newUserEntity->setState([
            'email' => 'admin@ushahidi.com',
            'realname' => 'Admin User',
            'role' => 'admin',
            'password' => 'admin' ]); // NOTE: pw is implicitly hashed via create
        $id = $this->userRepository->create($newUserEntity);
        $this->info("Created admin user with Id: ".$id);
    }
}
