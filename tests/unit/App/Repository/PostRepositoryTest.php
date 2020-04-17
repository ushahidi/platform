<?php
/**
 * Unit tests for Ushahidi\App\Repository\Post\Repository
 *
 * @author     Artur Neumann <artur@jankaritech.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2020 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
namespace Tests\Unit\App\Repository;

use Aura\Di\Injection\Factory;
use Ushahidi\App\Repository\Form\StageRepository;
use Ushahidi\App\Repository\PostRepository;
use PHPUnit\Framework\TestCase;
use Ushahidi\Core\Entity\User;
use Ushahidi\Core\Entity\FormRepository as FormRepositoryContract;
use Ushahidi\Core\Entity\FormAttributeRepository as FormAttributeRepositoryContract;
use Ushahidi\Core\Entity\PostLockRepository;
use Ushahidi\Core\Entity\PostValueRepository;
use Ushahidi\Core\Tool\Permissions\PostPermissions;
use Ushahidi\Core\Entity\ContactRepository;
use Ushahidi\App\Repository\Post\ValueFactory as PostValueFactory;
use Ushahidi\App\Multisite\OhanzeeResolver;

class PostRepositoryTest extends TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $postPermissions;
    /**
     * @var bool[]
     */
    private $permissions;

    public function setUp()
    {
        parent::setUp();
        $user = new User(['id' => 1]);

        $resolver = $this->createMock(OhanzeeResolver::class);

        $form_attribute_repo = $this->createMock(FormAttributeRepositoryContract::class);
        $form_stage_repo = $this->getMockBuilder(StageRepository::class)
            ->setMethods(['getHiddenStageIds'])
            ->disableOriginalConstructor()
            ->getMock();

        $form_repo = $this->createMock(FormRepositoryContract::class);
        $post_lock_repo = $this->createMock(PostLockRepository::class);
        $contact_repo = $this->createMock(ContactRepository::class);
        $post_value_factory = $this->getMockBuilder(PostValueFactory::class)
            ->setMethods(['getRepo'])
            ->disableOriginalConstructor()
            ->getMock();
        $bounding_box_factory = $this->createMock(Factory::class);


        $this->repository = $this->getMockBuilder(PostRepository::class)
            ->setConstructorArgs(
                [
                    $resolver,
                    $form_attribute_repo,
                    $form_stage_repo,
                    $form_repo,
                    $post_lock_repo,
                    $contact_repo,
                    $post_value_factory,
                    $bounding_box_factory
                ]
            )
            ->setMethods([
                'getUser', 'getTagsForPost', 'getSetsForPost', 'getCompletedStagesForPost',
                'getHydratedLock'
            ])
            ->getMock();

        $this->repository->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));
        $this->repository->expects($this->any())
            ->method('getTagsForPost')
            ->will($this->returnValue([1]));
        $this->repository->expects($this->any())
            ->method('getSetsForPost')
            ->will($this->returnValue([1]));
        $this->repository->expects($this->any())
            ->method('getCompletedStagesForPost')
            ->will($this->returnValue([1]));
        $this->repository->expects($this->any())
            ->method('getHydratedLock')
            ->will($this->returnValue([123]));

        $postValueRepository = $this->getMockBuilder(
            PostValueRepository::class
        )
            ->setMethods(['hideLocation', 'get', 'getValueQuery', 'getValueTable','hideTime'])
            ->disableOriginalConstructor()
            ->getMock();
        $postValueRepository->expects($this->any())
            ->method('hideLocation')
            ->will($this->returnValue(false));

        $post_value_factory->expects($this->any())
            ->method('getRepo')
            ->will($this->returnValue($postValueRepository));


        $this->postPermissions = $this->getMockBuilder(PostPermissions::class)
            ->setMethods(
                [
                    'canUserReadPrivateValues',
                    'canUserSeeLocation',
                    'canUserSeeTime',
                    'canUserSeeAuthor',
                    'canUserSeePostLock'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->permissions = [
            'canUserReadPrivateValues' => false,
            'canUserSeeLocation' => false,
            'canUserSeeTime' => true,
            'canUserSeeAuthor' => true,
            'canUserSeePostLock' => false
        ];
        $form_stage_repo->expects($this->any())
            ->method('getHiddenStageIds')
            ->will($this->returnValue([1]));

        $this->repository->setPostPermissions($this->postPermissions);
    }

    public function dataProvider()
    {
        return [
            [null, ['id' => null, 'parent_id' => null, 'form_id' => null]],
            [[''], ['id' => null, 'parent_id' => null, 'form_id' => null]],
            [['id' => null], ['id' => null, 'parent_id' => null, 'form_id' => null]],
            [['id' => 0], ['id' => 0, 'parent_id' => null, 'form_id' => null]],
            [['id' => null, 'form_id' => 1, 'status' => 1], ['id' => null, 'parent_id' => null, 'form_id' => 1]],
            [
                ['id' => null, 'form_id' => 1, 'status' => 1, 'title' => 'abc'],
                ['id' => null, 'parent_id' => null, 'form_id' => 1, 'title' => 'abc']
            ],

            //status becomes string
            [
                ['id' => 1, 'form_id' => 1, 'status' => 1],
                ['id' => 1, 'parent_id' => null, 'form_id' => 1, 'status' => '1']
            ],
            [
                [
                    'id' => 1,
                    'form_id' => 1,
                    'status' => 1,
                    'author_realname' => 'Artur Neumann',
                    'author_email' => 'me@ushahidi.com'
                ],
                ['id' => 1, 'form_id' => 1, 'author_realname' => 'Artur Neumann', 'author_email' => 'me@ushahidi.com']
            ],
            [
                [
                    'id' => 1,
                    'form_id' => 1,
                    'status' => 1,
                    'author_realname' => 'Artur Neumann',
                    'author_email' => 'me@ushahidi.com'
                ],
                ['id' => 1, 'form_id' => 1, 'author_realname' => null, 'author_email' => null],
                ['canUserSeeAuthor' => false]
            ],
            [
                ['id' => 1, 'form_id' => 1, 'status' => 1],
                ['id' => 1, 'form_id' => 1, 'lock' => [123]],
                ['canUserSeePostLock' => true]
            ],
            [
                ['id' => 1, 'form_id' => 1, 'status' => 1],
                ['id' => 1, 'form_id' => 1, 'lock' => null],
                ['canUserSeePostLock' => false]
            ],
            [
                [
                    'id' => 1,
                    'form_id' => 1,
                    'status' => 1,
                    'post_date' => '2020-01-02 07:08:09',
                    'created' => 1587109817,
                    'updated' => 1587109818
                ],
                [
                    'id' => 1,
                    'form_id' => 1,
                    'post_date' => new \DateTime('2020-01-02 07:08:09'),
                    'created' => 1587109817,
                    'updated' => 1587109818
                ],
                ['canUserSeeTime' => true]
            ],
            [
                [
                    'id' => 1,
                    'form_id' => 1,
                    'status' => 1,
                    'post_date' => '2020-01-02 07:08:09',
                    'created' => 1587109817,
                    'updated' => 1587109818
                ],
                [
                    'id' => 1,
                    'form_id' => 1,
                    'post_date' => new \DateTime('2020-01-02 00:00:00'),
                    'created' => 1587081600,
                    'updated' => 1587081600
                ],
                ['canUserSeeTime' => false]
            ],
            [
                [
                    'id' => 1,
                    'form_id' => 1,
                    'status' => 1,
                    'post_date' => '2020-01-02 07:08:09',
                    'created' => 'invalid',
                    'updated' => 'invalid'
                ],
                [
                    'id' => 1,
                    'form_id' => 1,
                    'post_date' => new \DateTime('2020-01-02 00:00:00'),
                    'created' => 0,
                    'updated' => 0
                ],
                ['canUserSeeTime' => false]
            ],
            [
                [
                    'id' => 1,
                    'form_id' => 1,
                    'status' => 1,
                    'post_date' => '2020-01-02 07:08:09',
                    'created' => 'invalid',
                    'updated' => 'invalid'
                ],
                [
                    'id' => 1,
                    'form_id' => 1,
                    'post_date' => new \DateTime('2020-01-02 07:08:09'),
                    'created' => 0,
                    'updated' => 0
                ],
                ['canUserSeeTime' => true]
            ],
            [
                [
                    'id' => 1,
                    'form_id' => 1,
                    'status' => 1,
                    'post_date' => 'invalid',
                    'created' => 'invalid',
                    'updated' => 'invalid'
                ],
                [
                    'id' => 1,
                    'form_id' => 1,
                    'post_date' => new \DateTime('2020-01-02 00:00:00'),
                    'created' => 0,
                    'updated' => 0
                ],
                ['canUserSeeTime' => false],
                \Exception::class
            ],
            //for id=0 the canUserSeeTime permission does not apply
            [
                [
                    'id' => 0,
                    'form_id' => 1,
                    'status' => 1,
                    'post_date' => '2020-01-02 07:08:09',
                    'created' => 1587109817,
                    'updated' => 1587109818
                ],
                [
                    'id' => 0,
                    'form_id' => 1,
                    'post_date' => new \DateTime('2020-01-02 07:08:09'),
                    'created' => 1587109817,
                    'updated' => 1587109818
                ],
                ['canUserSeeTime' => false]
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param array $data
     * @param array $expectedPostData
     * @param array|null $permissions
     * @param \Exception|null $exceptedException
     */
    public function testGetEntityData(
        $data,
        $expectedPostData,
        $permissions = null,
        $exceptedException = null
    ) {
        if ($permissions !== null) {
            foreach ($permissions as $permission => $value) {
                $this->permissions[$permission] = $value;
            }
        }
        if ($exceptedException !== null) {
            $this->expectException($exceptedException);
        }
        foreach ($this->permissions as $permission => $value) {
            $this->postPermissions->expects($this->any())
                ->method($permission)
                ->will($this->returnValue($value));
        }

        $post = $this->repository->getEntity($data);
        foreach ($expectedPostData as $key => $value) {
            $message = "value of key " . $key . " in post not as expected with input\n" . print_r($data, true);
            if ($value instanceof \DateTime) {
                $this->assertEquals($value, $post->$key, $message);
            } else {
                $this->assertSame($value, $post->$key, $message);
            }
        }
    }
}
