<?php

namespace Ushahidi\Tests\Unit\Multisite\Middleware;

use Mockery as M;
use Ushahidi\Tests\TestCase;
use Ushahidi\Multisite\Middleware\DetectSite as DetectSiteMiddleware;
use Ushahidi\Multisite\MultisiteManager;
use Ushahidi\Multisite\Site;
use Ushahidi\Multisite\SiteNotFoundException;

class DetectSiteTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
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
        $this->expectExceptionMessage('Your deployment is not ready yet. Please try again later.');

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
        $this->expectExceptionMessage('The deployment is down for maintenance.');

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
        $this->expectExceptionMessage('Your deployment is not ready yet. Please try again later.');

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
