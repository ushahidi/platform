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

// @todo consider just an Eloquent model? or ushahidi entity
class Site
{
    public $id;
    public $subdomain;
    public $domain;
    protected $deployment_name;
    public $deployed_date;
    public $db_host;
    public $db_name;
    public $db_username;
    public $db_password;
    protected $status;

    public function __construct(array $data)
    {
        // Assign all data to object
        foreach ($data as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDeploymentName()
    {
        return $this->deployment_name ?: 'Deployment';
    }

    /**
     * Get site client url
     * @return string
     */
    public function getClientUrl()
    {
        // @todo fetch from config
        // @todo handle non multsite case?
        return $this->subdomain . '.' . getenv('MULTISITE_CLIENT_DOMAIN');
    }

    /**
     * Get site base url
     * @return string
     */
    public function getBaseUrl() // fixme: naming. Technically a URN or URI. not a URL
    {
        return $this->subdomain . ($this->domain ? '.' . $this->domain : '');
    }

    public function getCdnPrefix()
    {
        return $this->subdomain . ($this->domain ? '.' . $this->domain : '');
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

    public function isDbReady()
    {
        /*
        $deploymentName = $deployment['deployment_name'] ? $deployment['deployment_name'] : 'Deployment';

        // Check we can connect to the DB
        try {
            DB::select(DB::expr('1'))->from('users')
                ->execute(Database::instance('deployment', $config));
        } catch (\Exception $e) {
            // If we can't connect, throw 503 Service Unavailable
            abort(503, $deploymentName . " is not ready");
        }
        */

        // @todo implement me
        return true;
    }

    public function getDbConfig()
    {
        return [
            'host'     => $this->db_host,
            'database' => $this->db_name,
            'username' => $this->db_username,
            'password' => $this->db_password,
        ];
    }
}
