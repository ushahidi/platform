<?php

namespace spec\Ushahidi\App\Multisite;

use Ushahidi\App\Multisite\MultisiteManager;
use Ushahidi\App\Multisite\Site;
use Ushahidi\App\Multisite\SiteRepository;
use Ushahidi\App\Multisite\SiteNotFoundException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Illuminate\Contracts\Events\Dispatcher;

class MultisiteManagerSpec extends ObjectBehavior
{
    function let(SiteRepository $repo, Dispatcher $events)
    {
        $config = [
            'enabled' => true,
            'domain'  => 'myushahidi.com'
        ];

        $this->beConstructedWith($config, $repo, $events);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MultisiteManager::class);
    }

    function it_can_parse_a_known_subdomain(Site $site, $repo)
    {
        $repo->getByDomain('abc', 'myushahidi.com')->willReturn($site);

        $this->setSiteFromHost('abc.myushahidi.com');

        $site = $this->getSite()->shouldReturnAnInstanceOf(Site::class);
    }

    function it_can_parse_a_known_domain(Site $site, $repo)
    {
        $repo->getByDomain('', 'customushahidi.com')->willReturn($site);

        $this->setSiteFromHost('customushahidi.com');

        $site = $this->getSite()->shouldReturnAnInstanceOf(Site::class);
    }

    function it_can_parse_an_unknown_subdomain(Site $site, $repo)
    {
        $repo->getByDomain('unknown', 'myushahidi.com')->willReturn(false);

        $this->shouldThrow(SiteNotFoundException::class)->during('setSiteFromHost', ['unknown.myushahidi.com']);
    }

    function it_can_set_site_by_id(Site $site, $repo)
    {
        $repo->getById(33)->willReturn($site);

        $this->setSiteById(33);

        $this->getSite()->shouldReturnAnInstanceOf(Site::class);
    }

    function it_return_false_when_no_site()
    {
        $this->getSite()->shouldReturn(false);

        $this->getSiteId()->shouldReturn(false);
    }
}
