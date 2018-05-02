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

class ExportTest extends TestCase
{
	public function setUp()
	{
		parent::setup();

		$this->messageRepo = M::mock(MessageRepository::class);
		$this->contactRepo = M::mock(ContactRepository::class);
		$this->targetedSurveyStateRepo = M::mock(TargetedSurveyStateRepository::class);
		$this->postRepo = M::mock(PostRepository::class);
		$this->formAttributeRepo = M::mock(FormAttributeRepository::class);

		$this->app->instance(CreatePostFromMessage::class, new CreatePostFromMessage(
			$this->messageRepo,
			$this->targetedSurveyStateRepo,
			$this->postRepo
		));
		$this->app->instance(HandleTargetedSurveyResponse::class, new HandleTargetedSurveyResponse(
			$this->messageRepo,
			$this->targetedSurveyStateRepo,
			$this->formAttributeRepo
		));

		$events = app('events');
		$events->subscribe(\Ushahidi\App\Subscriber::class);

		//$this->usecase = new ReceiveMessage();
		$this->usecase = service('factory.usecase')->get('messages', 'receive');
		$this->usecase
			->setRepository($this->messageRepo)
			->setContactRepository($this->contactRepo)
			->setDispatcher($events);

		// Ensure smssync is enabled
		app('datasources')->setEnabledSources(['smssync' => true]);
	}

	public function tearDown()
	{
		parent::tearDown();
	}


}