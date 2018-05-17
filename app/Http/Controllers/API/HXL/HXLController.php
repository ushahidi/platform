<?php

namespace Ushahidi\App\Http\Controllers\API\HXL;

use Ushahidi\App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ushahidi\App\Http\Controllers\RESTController;

/**
 * Demo HXL feature flag
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application\Controllers
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
use Germanazo\CkanApi\CkanApiClient;
use GuzzleHttp\Client;

class HXLController extends Controller
{
    /**
    * Retrieve a basic information about the API
    *
    * GET /api
    *
    * @return void
    */
    public function index()
    {
        // Example Use of Ckan CLient
        $api_key = '';
        // Example code
        $config = [
            'base_uri' => 'https://data.humdata.org/',
            'headers' => ['Authorization' => $api_key],
        ];

        $client = new Client($config);

        $ckanClient = new CkanApiClient($client);
        $data = [];

        //package_create
        // $data = $ckanClient->dataset()->create([
        // 'owner_org' => 'ushahidi',
        // 'name' => 'super-title',
        // 'title' => 'SUPER API TITLE'
        // ]);

        //package_show
        //$data = $ckanClient->dataset()->show();

        //package_update
        //$data = $ckanClient->dataset()->update();
        //resource_create
        //$data = $ckanClient->resource()->create();

        return [
            'hxl'       => $data
        ];
    }
}
