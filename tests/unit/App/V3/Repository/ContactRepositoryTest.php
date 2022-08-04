<?php

/**
 * Unit tests for ContactRepository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\App\V3\Repository;

use Ushahidi\Core\Entity\Contact;
use Ushahidi\Tests\TestCase;
use Ushahidi\Tests\DatabaseTransactions;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class ContactRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateMany()
    {
        // Generate contact data
        $contact1 = new Contact([
            'contact' => 'someone@ushahidi.com',
            'type' => 'email',
            'can_notify' => true,
        ]);
        $contact2 = new Contact([
            'contact' => '1234567',
            'type' => 'phone',
            'can_notify' => true,
        ]);
        $contact3 = new Contact([
            'contact' => 'twitterid',
            'type' => 'twitter',
            'can_notify' => false,
        ]);

        $repo = service('repository.contact');
        $inserted = $repo->createMany(collect([
            $contact1,
            $contact2,
            $contact3,
        ]));

        $this->assertCount(3, $inserted);
        $this->seeInOhanzeeDatabase('contacts', [
            'id' => $inserted[0],
            'contact' => 'someone@ushahidi.com',
            'can_notify' => 1,
        ]);
        $this->seeInOhanzeeDatabase('contacts', [
            'id' => $inserted[1],
            'contact' => '1234567',
            'can_notify' => 1,
        ]);
        $this->seeInOhanzeeDatabase('contacts', [
            'id' => $inserted[2],
            'contact' => 'twitterid',
            'can_notify' => 0,
        ]);
    }
}
