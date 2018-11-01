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

class MultisiteManager
{
    protected $enabled;
    protected $domain;
    protected $currentSite;

    /**
     * @param array $config
     */
    public function __construct($config, SiteRepository $repo)
    {
        $this->enabled = $config['enabled'];
        $this->domain = $config['domain'];
        $this->repo = $repo;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function enabled()
    {
        return $this->enabled;
    }

    /**
     * @param string $host
     * @return void
     * @throws SiteNotFoundException
     */
    public function setSiteFromHost($host)
    {
        // @todo validate host?

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
    public function setSiteById($siteId)
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
     * @param Site $site
     * @return void
     */
    public function setSite(Site $site)
    {
        $this->currentSite = $site;

        // Trigger DB changes, etc
        // event('multisite.site.changed', ['site' => $site]);
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
