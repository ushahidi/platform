<?php

namespace Tests\Unit\Ushahidi\App\Multisite;

use Illuminate\Contracts\Events\Dispatcher;
use Mockery as M;
use Tests\TestCase;
use Ushahidi\App\Multisite\MultisiteManager;
use Ushahidi\App\Multisite\Site;
use Ushahidi\App\Multisite\SiteNotFoundException;
use Ushahidi\App\Multisite\SiteRepository;

class MultisiteManagerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $config = [
            'enabled' => true,
            'domain'  => 'myushahidi.com',
        ];
        $this->repo = M::mock(SiteRepository::class);
        $this->events = M::spy(Dispatcher::class);

        $this->multisite = new MultisiteManager($config, $this->repo, $this->events);
    }

    public function testItCanParseAKnownSubdomain()
    {
        $this->repo->shouldReceive('getByDomain')->with('abc', 'myushahidi.com')->andReturn(M::mock(Site::class));

        $this->multisite->setSiteFromHost('abc.myushahidi.com');

        $this->assertInstanceOf(Site::class, $this->multisite->getSite());
    }

    public function testItCanParseAKnownDomain()
    {
        $this->repo->shouldReceive('getByDomain')->with('', 'customushahidi.com')->andReturn(M::mock(Site::class));

        $this->multisite->setSiteFromHost('customushahidi.com');

        $this->assertInstanceOf(Site::class, $this->multisite->getSite());
    }

    public function testItCanParseAnUnknownDomain()
    {
        $this->repo->shouldReceive('getByDomain')->with('unknown', 'myushahidi.com')->andReturn(false);

        $this->expectException(SiteNotFoundException::class);
        $this->multisite->setSiteFromHost('unknown.myushahidi.com');
    }

    public function testItCanSetSiteById()
    {
        $this->repo->shouldReceive('getById')->with(33)->andReturn(M::mock(Site::class));

        $this->multisite->setSiteById(33);

        $this->assertInstanceOf(Site::class, $this->multisite->getSite());
    }

    public function testItReturnsFalseWhenNoSite()
    {
        $this->assertFalse(
            $this->multisite->getSite()
        );

        $this->assertFalse(
            $this->multisite->getSiteId()
        );
    }
}
