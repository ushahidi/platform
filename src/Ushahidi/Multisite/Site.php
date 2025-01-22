<?php

/**
 * Site Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Multisite;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ushahidi\Core\Entity\Site as BaseSite;

class Site extends BaseSite
{
    protected $subdomain;

    protected $tier;

    protected $deployed_date;

    protected $expiration_date;

    protected $extension_date;

    protected $status;

    protected $db_host;

    protected $db_host_replica;

    protected $db_name;

    protected $db_username;

    protected $db_password;

    /**
     * Get deployment email from multisite config or site config
     *
     * @return string
     */
    public function getEmail()
    {
        // If we're in multisite mode
        if (config('multisite.enabled') && $multisite_email = config('multisite.email')) {
            // use multisite email
            return $multisite_email;
        }

        return parent::getEmail();
    }

    /**
     * Get site client url
     *
     * @return string
     */
    public function getClientUri()
    {
        // If we're in multisite mode
        if (config('multisite.enabled')) {
            // build the url from config + subdomain
            $http_host = implode('.', array_filter([
                $this->subdomain,
                config(
                    'multisite.client_domain',
                    $this->domain
                )
            ]));

            return config('multisite.client_scheme') . "://$http_host";
        }

        // get client_url from site config
        return parent::getClientUri();
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
     * Check if db is ready.
     * If there is an exception due to a missing table or any reason we just let users know the db isn't ready
     * @return boolean
     */
    public function isDbReady()
    {
        try {
            // @todo confirm this can't use the wrong db
            $connection = DB::connection('deployment-' . $this->id);
            return $connection->getSchemaBuilder()->hasTable('users');
        } catch (\Exception $e) {
            Log::warning($e->getMessage() . PHP_EOL . 'Database for deployment-' . $this->id . ' is not ready.');
            Log::debug($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Get db config
     * @return array
     */
    public function getDbConfig()
    {
        return [
            'host' => $this->db_host,
            'write' => [
                'host' => $this->db_host,
            ],
            'read' => [
                'host' => !empty($this->db_host_replica) ? $this->db_host_replica : $this->db_host
            ],
            'database' => $this->db_name,
            'username' => $this->db_username,
            'password' => $this->db_password,
        ];
    }
}
