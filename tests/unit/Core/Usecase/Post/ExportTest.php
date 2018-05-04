<?php
/**
 * Created by PhpStorm.
 * User: rowasc
 * Date: 5/2/18
 * Time: 1:36 PM
 */

namespace Tests\Unit\Core\Usecase\Post;

use Tests\TestCase;
use Mockery as M;
use Ushahidi\App\Repository\ExportJobRepository;
use Ushahidi\App\Repository\Form\AttributeRepository;
use Ushahidi\App\Repository\Post\ExportRepository;
use Ushahidi\Core\Entity\ExportJob;
use Ushahidi\Core\Entity\MockExportJobEntity;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\SearchData;

class ExportTest extends TestCase
{
	public function setUp()
	{
		parent::setup();

		$this->postExportRepository = M::mock(ExportRepository::class);
		$this->exportJobRepository = M::mock(ExportJobRepository::class);
		$this->formAttributeRepository = M::mock(AttributeRepository::class);
		$this->usecase = service('factory.usecase')->get('posts_export', 'export');

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
	}

	public function testJobIsUpdated()
    {
		// set CLI params to be the payload for the usecase
		$payload = [
			'job_id' => 1,
			'limit' => 100,
			'offset' => 0,
			'add_header' => true,
		];

		$post1 = new MockPostEntity();
		$post2 = new MockPostEntity();
		$post1->setStateValue(
			'post_date',
            '2017-02-22'
		);
		$post2->setStateValue(
			'post_date',
            '2017-02-22'
		);
//		$post1->shouldReceive('asArray')
//			->andReturn([$post1, $post2]);
//		$post2->shouldReceive('asArray')
//			->andReturn([$post1, $post2]);

		$jobRepoSpy = \Mockery::mock(ExportJobRepository::class);
		$searchDataMock = M::type(SearchData::class);

		$this->postExportRepository->shouldReceive('setSearchParams')
			->once()
			->with($searchDataMock)
			->andReturn(null);
		$this->postExportRepository->shouldReceive('retrieveMetaData')
			->once()
			->andReturn([]);
		$this->postExportRepository->shouldReceive('getSearchResults')
			->once()
			->andReturn([$post1, $post2]);

		$jobRepoSpy
			->shouldReceive('setSearchParams')
			->with(['filters' => []])
			->andReturn(null);
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
					'priority' => 1
				],
				[
					'label' => 'Post Status',
					'key' => 'status',
					'type' => 'string',
					'input' => 'string',
					'form_id' => 0,
					'form_stage_id' => 0,
					'form_stage_priority' => 0,
					'priority' => 2
				],
				[
					'label' => 'Created (UTC)',
					'key' => 'created',
					'type' => 'datetime',
					'input' => 'native',
					'form_id' => 0,
					'form_stage_id' => 0,
					'form_stage_priority' => 0,
					'priority' => 3
				],
				[
					'label' => 'Updated (UTC)',
					'key' => 'updated',
					'type' => 'datetime',
					'input' => 'native',
					'form_id' => 0,
					'form_stage_id' => 0,
					'form_stage_priority' => 0,
					'priority' => 4
				],
				[
					'label' => 'Post Date (UTC)',
					'key' => 'post_date',
					'type' => 'datetime',
					'input' => 'native',
					'form_id' => 0,
					'form_stage_id' => 0,
					'form_stage_priority' => 0,
					'priority' => 5
				],
				[
					'label' => 'Contact ID',
					'key' => 'contact_id',
					'type' => 'integer',
					'input' => 'number',
					'form_id' => 0,
					'form_stage_id' => 0,
					'form_stage_priority' => 0,
					'priority' => 6
				],
				[
					'label' => 'Contact',
					'key' => 'contact',
					'type' => 'text',
					'input' => 'text',
					'form_id' => 0,
					'form_stage_id' => 0,
					'form_stage_priority' => 0,
					'priority' => 7
				],
				[
					'label' => 'Sets',
					'key' => 'sets',
					'type' => 'sets',
					'input' => 'text',
					'form_id' => 0,
					'form_stage_id' => 0,
					'form_stage_priority' => 0,
					'priority' => 8
				]]);

		$this->usecase
			->setExportJobRepository($jobRepoSpy);

		$exportJobEntity = new \Tests\Unit\Core\Usecase\Post\MockExportJobEntity();
		$exportJobEntity->user_id = 1;
		$exportJobEntity->id = 11;
		$exportJobEntity->setState([
			'post_date' => '2017-02-22',
		]);

		$jobRepoSpy
			->shouldReceive('get')
			->with(1)
			->andReturn($exportJobEntity);
		$jobRepoSpy->shouldReceive('update')
			->with($exportJobEntity)
			->andReturn(11);
		$this->usecase
			->setPayload($payload)
			->interact();
	}
}
