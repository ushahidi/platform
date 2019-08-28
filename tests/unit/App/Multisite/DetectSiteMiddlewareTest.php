<?php

namespace Tests\Unit\App\Multisite;

use Ushahidi\App\Multisite\DetectSiteMiddleware;
use Ushahidi\App\Multisite\MultisiteManager;
use Ushahidi\App\Multisite\SiteNotFoundException;
use Ushahidi\App\Multisite\Site;
use Tests\TestCase;
use Mockery as M;

class DetectSiteMiddlewareTest extends TestCase
{

    public function testNothingCalledWhenDisabled()
    {
        $multisite = M::mock(MultisiteManager::class);
        $middleware = new DetectSiteMiddleware($multisite);
        $request = M::mock(\Illuminate\Http\Request::class);

        $multisite->shouldReceive('enabled')->andReturn(false);

        $middleware->handle($request, function () {
        });
    }

    public function testSiteNotFound()
    {
        $multisite = M::mock(MultisiteManager::class);
        $middleware = new DetectSiteMiddleware($multisite);
        $request = M::mock(\Illuminate\Http\Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('getHost')->andReturn('host.ushahidi.io');
        $multisite->shouldReceive('setSiteFromHost')
            ->with('host.ushahidi.io')
            ->andThrow(new SiteNotFoundException());

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $this->expectExceptionMessage('Deployment not found');

        $middleware->handle($request, function () {
        });
    }

    public function testSiteNotReady()
    {
        $multisite = M::mock(MultisiteManager::class);
        $middleware = new DetectSiteMiddleware($multisite);
        $request = M::mock(\Illuminate\Http\Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('getHost')->andReturn('host.ushahidi.io');
        $multisite->shouldReceive('setSiteFromHost')
            ->with('host.ushahidi.io');

        $site = M::mock(Site::class);
        $multisite->shouldReceive('getSite')->andReturn($site);

        $site->shouldReceive('getStatus')->andReturn('pending');
        $site->shouldReceive('getName')->andReturn('A deployment');

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('A deployment is not ready');

        $middleware->handle($request, function () {
        });
    }

    public function testSiteInMaintenance()
    {
        $multisite = M::mock(MultisiteManager::class);
        $middleware = new DetectSiteMiddleware($multisite);
        $request = M::mock(\Illuminate\Http\Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('getHost')->andReturn('host.ushahidi.io');
        $multisite->shouldReceive('setSiteFromHost')->with('host.ushahidi.io');

        $site = M::mock(Site::class);
        $multisite->shouldReceive('getSite')->andReturn($site);

        $site->shouldReceive('getStatus')->andReturn('maintenance');
        $site->shouldReceive('getName')->andReturn('A deployment');

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('A deployment is down for maintenance');

        $middleware->handle($request, function () {
        });
    }

    public function testSiteDbNotReady()
    {
        $multisite = M::mock(MultisiteManager::class);
        $middleware = new DetectSiteMiddleware($multisite);
        $request = M::mock(\Illuminate\Http\Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('getHost')->andReturn('host.ushahidi.io');
        $multisite->shouldReceive('setSiteFromHost')->with('host.ushahidi.io');

        $site = M::mock(Site::class);
        $multisite->shouldReceive('getSite')->andReturn($site);

        $site->shouldReceive('getStatus')->andReturn('deployed');
        $site->shouldReceive('getName')->andReturn('A deployment');
        $site->shouldReceive('isDbReady')->andReturn(false);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionMessage('A deployment is not ready');

        $middleware->handle($request, function () {
        });
    }

    public function testSiteSetSuccesfully()
    {
        $multisite = M::mock(MultisiteManager::class);
        $middleware = new DetectSiteMiddleware($multisite);
        $request = M::mock(\Illuminate\Http\Request::class);

        $multisite->shouldReceive('enabled')->andReturn(true);
        $request->shouldReceive('getHost')->andReturn('host.ushahidi.io');
        $multisite->shouldReceive('setSiteFromHost')->with('host.ushahidi.io');

        $site = M::mock(Site::class);
        $multisite->shouldReceive('getSite')->andReturn($site);

        $site->shouldReceive('getStatus')->andReturn('deployed');
        $site->shouldReceive('getName')->andReturn('A deployment');
        $site->shouldReceive('isDbReady')->andReturn(true);

        $result = $middleware->handle($request, function () {
            return 'called';
        });
        $this->assertEquals($result, 'called');
    }
}
