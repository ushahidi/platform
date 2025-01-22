<?php

/**
 * Unit tests for Ushahidi\Modules\V3\Repository\Post\ValueRepository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Tests\Unit\Modules\V3\Repository;

use Ushahidi\Core\Entity\PostValue;
use Ushahidi\Modules\V3\Repository\Post\ValueRepository;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class PostValueRepositoryTest extends \PHPUnit\Framework\TestCase
{
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getMockBuilder(ValueRepository::class)
            ->setMethods(['selectOne', 'selectQuery', 'getTable', 'db'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->postvalue = $this->createMock(PostValue::class);
    }

    /**
     * Test get method
     */
    public function testGet()
    {
        $this->repository->expects($this->any())
            ->method('selectOne')
            ->will($this->returnValue([
                'id' => 1,
                'value' => 'somevalue',
            ]));

        // Check that get() returns a PostValue Entity
        $this->assertInstanceOf(PostValue::class, $this->repository->get(1));

        // Check entity returned by get() has expected values
        $entity = $this->repository->get(1);
        $this->assertEquals(1, $entity->id);
        $this->assertEquals('somevalue', $entity->value);
    }

    /**
     * Test get method
     */
    public function testGetAllForPost()
    {
        // Create mocks
        $mockQueryBuilder = $this->getMockBuilder('Database_Query_Builder_Select')
            ->setMethods(['execute', 'where'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockResult = $this->getMockBuilder('Database_Result')
            ->setMethods(['as_array', '__destruct', 'seek', 'current'])
            ->disableOriginalConstructor()
            ->getMock();

        // Set expectations to provide fake db results
        $this->repository->expects($this->any())
            ->method('selectQuery')
            ->will($this->returnValue($mockQueryBuilder));

        $mockQueryBuilder->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($mockResult));

        $mockResult->expects($this->any())
            ->method('as_array')
            ->will($this->returnValue([
                [
                    'id' => 1,
                    'value' => 'one',
                ],
                [
                    'id' => 2,
                    'value' => 'two',
                ],
                [
                    'id' => 3,
                    'value' => 'three',
                ],
            ]));

        // Check that getAllForPost() returns an array of PostValue's
        $values = $this->repository->getAllForPost(1);
        $this->assertCount(3, $values);
        $this->assertInstanceOf(PostValue::class, current($values));
    }
}
