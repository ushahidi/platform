<?php

/**
 * Unit tests for Ushahidi_Repository_PostValue
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Core\Traits;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class ReceiveMessageTest extends \PHPUnit\Framework\TestCase
{
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->repository = $this->getMockBuilder(\Ushahidi_Repository_Post_Value::class)
            ->setMethods(['selectOne', 'selectQuery', 'getTable'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->postvalue = $this->createMock(\Ushahidi\Core\Entity\PostValue::class);
    }
}
