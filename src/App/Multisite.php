<?php
/**
 * Ushahidi Multisite
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App;

use Ohanzee\DB;
use Ohanzee\Database;
use Illuminate\Http\Request;

class Multisite
{
    protected $db;
    protected $domain;
    protected $subdomain;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    protected function parseHost($host)
    {
        if (!$this->domain && !$this->subdomain) {
            // Load the default domain
            // @todo stop call config directly
            $domain = config('multisite.domain');

            // If no host passed in, check the for HOST in environment
            if (!$host) {
                $host = getenv('HOST');
            }
            // If we still don't have a host
            if (! $host) {
                // @todo we should try app('request') first but we can't guarantee its been created
                $request = Request::capture();
                // .. parse the current URL
                $host = $request->getHost();
            }

            // If we still don't have a host
            if (! $host) {
                // Finally fallback to just $_SERVER vars
                // Or just no subdomain if we can't figure it out
                $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $domain;
            }

            // If $domain is set and we're at a subdomain of $domain..
            if ($domain and substr($host, strlen($domain) * -1) == $domain) {
                // .. grab just the subdomain
                $subdomain = substr($host, 0, (strlen($domain) * -1) -1);
            } else {
                // .. otherwise grab the whole domain
                $domain = $host;
                $subdomain = '';
            }

            $this->domain = $domain;
            $this->subdomain = $subdomain;
        }
    }

    public function getDbConfig($host = null)
    {
        $this->parseHost($host);

        // If we're running in the CLI and we can't get a subdomain
        // just return the multisite db
        if (app()->runningInConsole() && $this->subdomain === false && $this->domain === config('multisite.domain')) {
            return config('ohanzee-db.multisite');
        }

        // find the current deployment credentials
        $result = DB::select()->from('deployments')
            ->where('subdomain', '=', $this->subdomain)
            ->where('domain', '=', $this->domain)
            ->limit(1)
            ->offset(0)
            // @todo filter only active deployments?
            ->execute($this->db);

        $deployment = $result->current();

        $this->checkDeploymentStatus($deployment);

        // Set new database config
        // @todo stop call config directly
        $config = config('ohanzee-db.default');

        $config['connection'] = [
            'hostname'   => $deployment['db_host'],
            'database'   => $deployment['db_name'],
            'username'   => $deployment['db_username'],
            'password'   => $deployment['db_password'],
            'persistent' => $config['connection']['persistent'],
        ];

        $this->checkDeploymentDbConnection($config);

        return $config;
    }

    public function getCdnPrefix($host = null)
    {
        $this->parseHost($host);

        return $this->subdomain . ($this->domain ? '.' . $this->domain : '');
    }

    public function getSite($host = null)
    {
        $this->parseHost($host);
        return $this->subdomain . ($this->domain ? '.' . $this->domain : '');
    }

    public function getClientUrl($host = null)
    {
        $this->parseHost($host);

        return $this->subdomain . '.' . getenv('MULTISITE_CLIENT_DOMAIN');
    }
    protected function checkDeploymentStatus($deployment)
    {
        $status = $deployment['status'];
        $deployedDate = $deployment['deployed_date'];
        $deploymentName = $deployment['deployment_name'] ? $deployment['deployment_name'] : 'Deployment';

        // No deployment? throw a 404
        if (! count($deployment)) {
            abort(404, $deploymentName . " not found");
        } elseif (($status === 'migrating' && !$deployedDate) || $status === 'pending') {
            abort(503, $deploymentName . " is not ready");
        } elseif (($status === 'migrating' && $deployedDate) || $status === 'maintenance' || $status === 'importing') {
            abort(503, $deploymentName . " is down for maintenance");
        }
    }
    protected function checkDeploymentDbConnection($config)
    {
        // Check we can connect to the DB
        try {
            DB::select(DB::expr('1'))->from('users')
                ->execute(Database::instance('deployment', $config));
        } catch (Exception $e) {
            // If we can't connect, throw 503 Service Unavailable
            abort(503, $this->domain . "is not ready");
        }
    }
}
