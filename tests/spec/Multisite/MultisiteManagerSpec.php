<?php

namespace spec\Ushahidi\Multisite;

use Illuminate\Contracts\Events\Dispatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Ushahidi\Multisite\MultisiteManager;
use Ushahidi\Multisite\Site;
use Ushahidi\Multisite\SiteNotFoundException;
use Ushahidi\Multisite\SiteRepository;

class MultisiteManagerSpec extends ObjectBehavior
{
    public function let(SiteRepository $repo, Dispatcher $events)
    {
        $config = [
            'enabled' => true,
            'domain'  => 'myushahidi.com',
        ];

        $this->beConstructedWith($config, $repo, $events);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MultisiteManager::class);
    }

    public function it_can_parse_a_known_subdomain(Site $site, $repo)
    {
        $repo->getByDomain('abc', 'myushahidi.com')->willReturn($site);

        $this->setSiteFromHost('abc.myushahidi.com');

        $site = $this->getSite()->shouldReturnAnInstanceOf(Site::class);
    }

    public function it_can_parse_a_known_domain(Site $site, $repo)
    {
        $repo->getByDomain('', 'customushahidi.com')->willReturn($site);

        $this->setSiteFromHost('customushahidi.com');

        $site = $this->getSite()->shouldReturnAnInstanceOf(Site::class);
    }

    public function it_can_parse_an_unknown_subdomain(Site $site, $repo)
    {
        $repo->getByDomain('unknown', 'myushahidi.com')->willReturn(false);

        $this->shouldThrow(SiteNotFoundException::class)->during('setSiteFromHost', ['unknown.myushahidi.com']);
    }

    public function it_can_set_site_by_id(Site $site, $repo)
    {
        $repo->getById(33)->willReturn($site);

        $this->setSiteById(33);

        $this->getSite()->shouldReturnAnInstanceOf(Site::class);
    }

    public function it_return_false_when_no_site()
    {
        $this->getSite()->shouldReturn(false);

        $this->getSiteId()->shouldReturn(false);
    }
}
