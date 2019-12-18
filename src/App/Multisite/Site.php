<?php

/**
 * Site Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Multisite;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Ushahidi\Core\Entity\ConfigRepository;

// @todo consider just an Eloquent model? or ushahidi entity
class Site
{
    /**
     * Cache lifetime in minutes
     */
    const CACHE_LIFETIME = 1;

    public $id;
    public $subdomain;
    public $domain;
    protected $deployment_name;
    public $deployed_date;
    public $expiration_date;
    public $extension_date;
    public $db_host;
    public $db_host_replica;
    public $db_name;
    public $db_username;
    public $db_password;
    protected $status;
    public $tier;

    public function __construct(array $data)
    {
        // Assign all data to object
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }
    }

    /**
     * Get site id
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * Get site config
     *
     * @param  mixed $param   param to return
     * @param  mixed $default default if param not set
     * @return mixed
     */
    public function getSiteConfig($param = false, $default = null)
    {
        $siteConfig = Cache::remember('config.site', self::CACHE_LIFETIME, function () {
            // @todo inject repo
            return app(ConfigRepository::class)->get('site');
        });

        if ($param) {
            return $siteConfig->$param ?? $default;
        }

        return $siteConfig;
    }

    /**
     * Get deployment email from multisite config or site config
     *
     * @return string
     */
    public function getEmail()
    {
        // @todo can I avoid loading this from site?
        $multisite_email = config('multisite.email');

        // If we're in multisite mode
        if (config('multisite.enabled') && $multisite_email) {
            // use multisite email
            return $multisite_email;
        } elseif ($site_email = $this->getSiteConfig('email')) {
            // Otherwise get email from site config
            return $site_email;
        } else {
            // Get host from lumen
            // @todo handle missing request?
            $host = app('request')->getHost();
            return $host ? 'noreply@' . $host : false;
        }
    }

    /**
     * Get site client url
     *
     * @return string
     */
    public function getClientUri()
    {
        // @todo this feels kind like mixing responsibilities?
        // If we're in multisite mode
        if (config('multisite.enabled')) {
            // build the url from config + subdomain
            return implode('.', array_filter([$this->subdomain, config('multisite.client_domain', $this->domain)]));
        } else {
            // get client_url from site config
            return $this->getSiteConfig('client_url', false);
        }
    }

    /**
     * Get site base url
     * @return string
     */
    public function getBaseUri()
    {
        return implode('.', array_filter([$this->subdomain, $this->domain]));
    }

    public function getCdnPrefix()
    {
        return implode('.', array_filter([$this->subdomain, $this->domain]));
    }

    /**
     * Get normalized deployment status
     * @return string
     */
    public function getStatus()
    {
        // Return any status before the initial deployment as 'pending'
        if (($this->status === 'migrating' && !$this->deployed_date) ||
            $this->status === 'pending' ||
            $this->status === 'deploying'
        ) {
            return 'pending';
        }

        // Normalize various maintenance statuses
        if (($this->status === 'migrating' && $this->deployed_date) ||
            $this->status === 'maintenance' ||
            $this->status === 'importing'
        ) {
            return 'maintenance';
        }

        // For anything else, just return the raw status
        return $this->status;
    }

    /**
     * Check if db is ready
     * @return boolean
     */
    public function isDbReady()
    {
        // @todo confirm this can't use the wrong db
        return DB::connection('deployment-'.$this->id)->getSchemaBuilder()->hasTable('users');
    }

    /**
     * Get db config
     * @return array
     */
    public function getDbConfig()
    {
        return [
            'host'     => $this->db_host,
            'write'     => [
                'host' => $this->db_host,
            ],
            'read'    => [
                'host' => !empty($this->db_host_replica) ? $this->db_host_replica : $this->db_host
            ],
            'database' => $this->db_name,
            'username' => $this->db_username,
            'password' => $this->db_password,
        ];
    }
}
