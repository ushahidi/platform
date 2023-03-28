<?php
/**
 * Created by PhpStorm.
 * User: rowasc
 * Date: 5/2/18
 * Time: 1:36 PM
 */

namespace Ushahidi\Tests\Unit\Core\Usecase\Post;

use Faker;
use Mockery as M;
use Ushahidi\Tests\TestCase;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Core\Entity\HXL\HXLLicense;
use Ushahidi\Core\Ohanzee\Entities\Post;
use Ushahidi\Core\Ohanzee\Entities\User;
use Ushahidi\Core\Entity\HXL\HXLMetadata;
use Ushahidi\Core\Ohanzee\Entities\ExportJob;
use Ushahidi\Core\Ohanzee\Repositories\ExportJobRepository;
use Ushahidi\Core\Ohanzee\Repositories\Post\ExportRepository;
use Ushahidi\Core\Ohanzee\Repositories\Form\AttributeRepository;

class ExportTest extends TestCase
{
    protected $jobId;

    protected $userId;

    protected $hxlMetaDataId;

    protected $hxlLicenseId;

    public function setUp()
    {
        parent::setup();
        $this->withoutMiddleware();

        $faker = Faker\Factory::create();

        $this->userId = service('repository.user')->create(new User([
            'email' => $faker->email,
            'password' => $faker->password(10),
            'realname' => $faker->name,
            'role' => 'admin',
        ]));

        $this->postExportRepository = M::mock(ExportRepository::class);
        $this->exportJobRepository = M::mock(ExportJobRepository::class);
        $this->formAttributeRepository = M::mock(AttributeRepository::class);

        $this->hxlLicenseId = service('repository.hxl_license')->create(new HXLLicense([
            'code' => 'ushahidi'.rand(),
            'name' => 'ushahidi-dataset',
            'link' => 'other',
        ]));

        $this->hxlMetaDataId = service('repository.hxl_meta_data')->create(new HXLMetadata([
            'license_id' => $this->hxlLicenseId,
            'organisation_id' => 'org-id-here',
            'organisation_name' => 'ushahidi',
            'dataset_title' => 'ushahidi-dataset',
            'source' => 'other',
            'private' => true,
            'user_id' => $this->userId,
        ]));
        $this->usecase = service('factory.usecase')->get('posts_export', 'export');
        $this->jobId = service('repository.export_job')->create(new ExportJob([
            'user_id' => $this->userId,
            'entity_type' => 'post',
            'hxl_meta_data_id' => $this->hxlMetaDataId,
        ]));
        // Get the usecase and pass in authorizer, payload and transformer
        $this->usecase = $this->usecase
            ->setAuthorizer(service('authorizer.export_job'))
            ->setFormatter(service('formatter.entity.post.csv'));
        $this->usecase->setExportJobRepository($this->exportJobRepository);
        $this->usecase->setFormAttributeRepository($this->formAttributeRepository);
        $this->usecase->setPostExportRepository($this->postExportRepository);
    }

    public function tearDown()
    {
        parent::tearDown();
        service('repository.hxl_license')->delete(
            new HXLLicense(['id' => $this->hxlLicenseId])
        );
        service('repository.hxl_meta_data')->delete(
            new HXLMetadata(['id' => $this->hxlMetaDataId])
        );
        service('repository.user')->delete(new User(['id' => $this->userId]));
        service('repository.export_job')->delete(new ExportJob(['id' => $this->jobId]));
    }

    public function testJobIsUpdated()
    {
        // set CLI params to be the payload for the usecase
        $payload = [
            'limit' => 100,
            'offset' => 0,
            'add_header' => true,
        ];

        $post1 = new Post();
        $post2 = new Post();
        $post1->setState([
            'post_date' => '2017-02-22',
        ]);
        $post2->setState([
            'post_date' => '2017-02-22',
        ]);
        $jobRepoSpy = \Mockery::mock(ExportJobRepository::class);
        $searchDataMock = M::type(SearchData::class);

        $this->postExportRepository->shouldReceive('setSearchParams')
            ->once()
            ->with($searchDataMock)
            ->andReturn(null);
        $this->postExportRepository->shouldReceive('retrieveMetaData')
            ->twice()//once per post
            ->andReturn([]);
        $this->postExportRepository->shouldReceive('getSearchResults')
            ->once()
            ->andReturn([$post1, $post2]);

        $jobRepoSpy
            ->shouldReceive('setSearchParams')
            ->with(['filters' => []])
            ->andReturn(null);
        $this->formAttributeRepository->shouldReceive('getFormsByAttributes')->once()
            ->with([])
            ->andReturn(
                [1]
            );
        $this->formAttributeRepository->shouldReceive('getExportAttributes')->once()
            ->with([])
            ->andReturn([
                [
                    'label' => 'Post ID',
                    'key' => 'id',
                    'type' => 'integer',
                    'input' => 'number',
                    'form_id' => 0,
                    'form_stage_id' => 0,
                    'form_stage_priority' => 0,
                    'priority' => 1,
                ], ]);

        $this->usecase
            ->setExportJobRepository($jobRepoSpy);

        $exportJobEntity = new ExportJob();
        $exportJobEntity->setState([
            'user_id' => $this->userId,
            'id' => $this->jobId,
            'post_date' => '2017-02-22',
        ]);
        $jobRepoSpy
            ->shouldReceive('get')
            ->with($this->jobId)
            ->andReturn($exportJobEntity);
        $jobRepoSpy->shouldReceive('update')
            ->with($exportJobEntity)
            ->andReturn($this->jobId);
        $results = $this->usecase
            ->setFilters($payload)
            ->setIdentifiers(['job_id' => $this->jobId])
            ->interact();

        $this->assertArrayHasKey('filename', $results);
    }
}
