<?php

namespace Tests\Unit\App\Repository;

use Mockery as M;
use Tests\TestCase;
use Ohanzee\Database;
use Ushahidi\Core\Tool\SearchData;
use Aura\Di\Injection\Factory;
use Ushahidi\Core\Entity\User;
use Ushahidi\Contracts\Session;
use Illuminate\Support\Collection;
use Ushahidi\App\Multisite\OhanzeeResolver;
use Ushahidi\App\Repository\PostRepository;
use Ushahidi\App\Repository\Post\ValueFactory;
use Ushahidi\Core\Tool\Permissions\PostPermissions;
use Ushahidi\Contracts\Repository\Entity\FormRepository;
use Ushahidi\Contracts\Repository\Entity\ContactRepository;
use Ushahidi\Contracts\Repository\Entity\PostLockRepository;
use Ushahidi\Contracts\Repository\Entity\FormStageRepository;
use Ushahidi\Contracts\Repository\Entity\FormAttributeRepository;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class PostRepositoryTest extends TestCase
{
    public function testSetSearchParamsLimitedUnprivileged()
    {
        $this->doTestSetSearchParams(0, 1, config('posts.list_max_limit'));
    }

    public function testSetSearchParamsLimitedPrivileged()
    {
        $this->doTestSetSearchParams(1, 1, config('posts.list_admin_max_limit'));
    }

    public function testSetSearchParamsNonlimitedUnprivileged()
    {
        $this->doTestSetSearchParams(0, 0);
    }

    public function testSetSearchParamsNonlimitedPrivileged()
    {
        $this->doTestSetSearchParams(1, 0);
    }

    /**
     * bool $canManagePosts
     * bool $limitPosts
     * int|null $expectedLimit
     */
    public function doTestSetSearchParams($canManagePosts, $limitPosts, $expectedLimit = null)
    {
        // we don't need to test anything but the LIMIT on the end of the sql so mock everything else in the db
        $db = M::mock(Database::class);
        $db->shouldReceive('quote_column');
        $db->shouldReceive('quote_table');
        $db->shouldReceive('quote');
        $resolver = M::mock(OhanzeeResolver::class);
        $resolver->shouldReceive('connection')->andReturn($db);
        $form_attribute_repo = M::mock(FormAttributeRepository::class);
        $form_stage_repo = M::mock(FormStageRepository::class);
        $attrs = M::mock(Collection::class);
        $attrs->shouldReceive('groupBy');
        $attrs->shouldReceive('keyBy');
        $form_repo = M::mock(FormRepository::class);
        $form_repo->shouldReceive('getAllFormStagesAttributes')->andReturn($attrs);
        $post_lock_repo = M::mock(PostLockRepository::class);
        $contact_repo = M::mock(ContactRepository::class);
        $post_value_factory = M::mock(ValueFactory::class);
        $bounding_box_factory = M::mock(Factory::class);

        $repo = new PostRepository(
            $resolver,
            $form_attribute_repo,
            $form_stage_repo,
            $form_repo,
            $post_lock_repo,
            $contact_repo,
            $post_value_factory,
            $bounding_box_factory
        );
        $user = new User();
        $session = M::mock(Session::class);
        $session->shouldReceive('getUser')->andReturn($user);
        $repo->setSession($session);

        $postPermissions = M::mock(PostPermissions::class);
        $postPermissions->shouldReceive('canUserManagePosts')->with($user)
            ->andReturn($canManagePosts)->times($limitPosts);
        $repo->setPostPermissions($postPermissions);

        $search = M::Mock(SearchData::class);
        $fakeLimit = 10000; // this limit should be overridden if limitPosts
        $search->shouldReceive('getSorting')->andReturn(['limit' => $fakeLimit]);
        $search->shouldReceive('getFilter')->with('limitPosts')->andReturn($limitPosts)->once();
        $search->shouldReceive('getFilter'); // we only care about limitPosts
        // run the test method
        $query = $repo->setSearchParams($search);
        // grab the resulting SQL and pull off the LIMIT clause on the end
        $sql = $query->compile($db);
        $limitPos = strpos($sql, 'LIMIT ');
        $limit = substr($sql, $limitPos+6);
        $expectedLimit = $expectedLimit ?? $fakeLimit;
        $this->assertEquals($expectedLimit, $limit);
    }
}
