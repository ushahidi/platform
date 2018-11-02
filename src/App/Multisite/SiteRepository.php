<?php

/**
 * Multsite Site Repo
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Multisite;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Facades\DB;

class SiteRepository
{
    protected $connection;

    public function __construct()
    {
        $this->connection = DB::connection('multisite');
    }

    public function getByDomain($subdomain, $domain)
    {
        $result = $this->connection->table('deployments')
            ->select('deployments.*', 'tiers.key as tier')
            ->join('tiers', 'tiers.id', '=', 'deployments.tier_id')
            ->where(compact('subdomain', 'domain'))
            ->first()
            ;

        return new Site(collect($result)->toArray());
    }

    public function getById($id)
    {
        $result = $this->connection->table('deployments')
            ->select('deployments.*', 'tiers.key as tier')
            ->join('tiers', 'tiers.id', '=', 'deployments.tier_id')
            ->where(compact('id'))
            ->first()
            ;

        return new Site(collect($result)->toArray());
    }
}
