<?php

/**
 * Site Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Ushahidi\Contracts\Repository\Entity\ConfigRepository;

class Site extends StaticEntity // Change to Basic Entity
{
    protected $id;

    protected $email;

    protected $domain;

    protected $deployment_name;

    public function getResource()
    {
        return 'site';
    }

    public function getDefinition()
    {
        [];
    }

    /**
     * Get site id
     * @return int|null
     */
    public function getId()
    {
        return $this->id ?? null;
    }

    /**
     * Get deployment name from deployments table or site config
     * @return string
     */
    public function getName()
    {
        return $this->getSiteConfig('name', $this->deployment_name ?: 'Deployment');
    }

    /**
     * Get deployment email from site config
     *
     * @return string
     */
    public function getEmail()
    {
        if ($site_email = $this->getSiteConfig('email')) {
            // Otherwise get email from site config
            return $site_email;
        }

        // Get host from app request
        // @todo handle missing request?
        $host = Request::getHost();
        return $host ? 'noreply@' . $host : false;
    }

    /**
     * Get site client url
     *
     * @return string
     */
    public function getClientUri()
    {
        // get client_url from site config
        return $this->getSiteConfig('client_url', false);
    }

    /**
     * Get site config
     *
     * @param  mixed $param   param to return
     * @param  mixed $default default if param not set
     * @return mixed
     */
    public function getSiteConfig($param = false, $default = null)
    {
        // TODO: I think there should be a way to work around this implementation
        // $siteConfig = Cache::remember('config.site', $this->cache_lifetime, function () {
        //     return app(ConfigRepository::class)->get('site')->asArray();
        // });

        // The work around using static entity
        $siteConfig = $this->asArray();

        if ($param) {
            return $siteConfig[$param] ?? $default;
        }

        return (object) $siteConfig;
    }
}
