<?php

/**
 * Unit tests for Ushahidi_Repository_PostValue
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Ushahidi;

use Ushahidi_Repository_Contact;
use Database_MySQLi;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class ContactRepositoryTest extends \PHPUnit\Framework\TestCase
{

    protected $repository;

    public function setUp()
    {
        parent::setUp();
        /* @TODO:
            Use a fixture / mock to setup some testable data.

        $dbconfig = [
            'connection' => ['username' => 'homestead',
            'hostname' => 'xxxxxxx',
            'username' => 'xxxxxxx',
            'database' => 'xxxxxxx',
            'password' => 'xxxxxx',
            'socket'   => '',
            'port'     => 3306]];
        $local_conn = '?';

        $db = new Database_MySQLi($local_conn, $dbconfig);
        $this->contactRepo = new Ushahidi_Repository_Contact($db); // not a mock!

        @TODO: add known targeted contact data
            e.g., $this->known_targeted_contact_phone = '2222222222';

        @TODO: add known contact data thats not in a survey
            e.g., $this->known_regular_contact_phone = '1111111111';

        */
    }


    public function testIsInTargetedSurvey()
    {
        /*
        $mock_payload = ['from'=> $this->known_targeted_contact_phone,
                        'contact_type' => 'sms'];
        $contact_mock = $this->contactRepo->getByContact($mock_payload['from'], $mock_payload['contact_type']);
        $this->assertEquals($this->contactRepo->isInTargetedSurvey($contact_mock->getId()), true );

        $mock_payload = ['from'=> $this->known_regular_contact_phone,
                        'contact_type' => 'sms'];

        $contact_mock = $this->contactRepo->getByContact($mock_payload['from'], $mock_payload['contact_type']);
        $this->assertEquals($this->contactRepo->isInTargetedSurvey($contact_mock->getId()), false );
        */
    }
}
