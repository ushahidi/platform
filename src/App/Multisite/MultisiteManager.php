<?php

/**
 * Ushahidi Platform
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Multisite;

use Illuminate\Contracts\Events\Dispatcher;

class MultisiteManager
{
    protected $enabled;
    protected $domain;
    protected $currentSite;

    /**
     * @param array $config
     */
    public function __construct($config, SiteRepository $repo, Dispatcher $events)
    {
        $this->enabled = $config['enabled'];
        $this->domain = $config['domain'];
        $this->repo = $repo;
        $this->events = $events;
    }

    public function getDomain() : string
    {
        return $this->domain;
    }

    public function enabled() : bool
    {
        return (bool) $this->enabled;
    }

    /**
     * @param string $host
     * @return void
     * @throws SiteNotFoundException
     */
    public function setSiteFromHost(string $host)
    {
        // Validate the host
        // This is very permissive filter. We're not using FILTER_FLAG_HOSTNAME
        // because it would block IDNs
        if (!filter_var($host, FILTER_VALIDATE_DOMAIN)) {
            throw new \InvalidArgumentException();
        }

        // If $domain is set and we're at a subdomain of $domain...
        if ($this->domain and substr($host, strlen($this->domain) * -1) == $this->domain) {
            // ... grab just the subdomain
            $subdomain = substr($host, 0, (strlen($this->domain) * -1) -1);
            // ... and search for the subdomain/domain combination
            $site = $this->repo->getByDomain($subdomain, $this->domain);
        } else {
            // ... otherwise search for the whole domain
            $site = $this->repo->getByDomain('', $host);
        }

        // If we didn't find the site...
        if (!$site) {
            // ... throw SiteNotFound
            throw new SiteNotFoundException();
        }

        $this->setSite($site);
    }

    /**
     * @param int $siteId
     * @return void
     * @throws SiteNotFoundException
     */
    public function setSiteById(int $siteId)
    {
        $site = $this->repo->getById($siteId);

        // If we didn't find the site...
        if (!$site) {
            // ... throw SiteNotFound
            throw new SiteNotFoundException();
        }

        $this->setSite($site);
    }

    /**
     * Set site to fake default for single site mode
     */
    public function setDefaultSite($domain = '')
    {
        $this->setSite(new Site(
            [
                'id' => 0,
                'status' => 'deployed',
                'domain' => $domain,
                'db_host' => config('database.connections.mysql.host'),
                'db_name' => config('database.connections.mysql.database'),
                'db_username' => config('database.connections.mysql.username'),
                'db_password' => config('database.connections.mysql.password'),
            ]
        ));
    }

    /**
     * @param Site $site
     * @return void
     */
    public function setSite(Site $site)
    {
        $this->currentSite = $site;

        // Trigger DB changes, etc
        $this->events->dispatch('multisite.site.changed', ['site' => $site]);
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->currentSite ?: false;
    }

    /**
     * @return Site
     */
    public function getSiteId()
    {
        return $this->currentSite ? $this->currentSite->getId() : false;
    }
}
