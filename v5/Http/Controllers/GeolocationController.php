<?php
/**
 * *
 *  * Ushahidi Acl
 *  *
 *  * @author     Ushahidi Team <team@ushahidi.com>
 *  * @package    Ushahidi\Application
 *  * @copyright  2020 Ushahidi
 *  * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 *
 *
 */

namespace v5\Http\Controllers;

use Geocoder\Query\GeocodeQuery;
use Geocoder\Query\ReverseQuery;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Ushahidi\App\Util\Tile;

class GeolocationController extends V4Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function query(Request $request)
    {
        /*
         * Expected parameters
         */


        $query = $request->get('query');
        // make nicer


        // qid will be used for querycache once we have that.
        $query_id = (integer) $request->get('qid');
        // should we group results by country/???
        $group_by = (boolean) $request->get('group_by') ?? true;
        $limit = (integer) $request->get('limit');
        $offset = (integer) $request->get('offset');
        $locale = $request->get('locale') ?: app('translator')->getLocale();
        // TODO call service to grab zoom,x,y from config
//        $viewbox = Tile::tileToBoundingBox($zoom, $x, $y);
        $httpClient = new \Http\Adapter\Guzzle6\Client();
        // TODO: use the right referrer ?
        $provider = new \Geocoder\Provider\Nominatim\Nominatim(
            $httpClient,
            "https://nominatim.openstreetmap.org",
            "Ushahidi Geolocation",
            "https://ushahidi.io"
        );

        $geocoder = new \Geocoder\StatefulGeocoder($provider, $locale);
        $results = $geocoder->geocodeQuery(
            GeocodeQuery::create($query)
        );

        /**
         * - use the deployer-set "preferred location" in search
         * - limited by a country set by the deployer (later)
         *  Tile::tileToBoundingBox($zoom, $x, $y); with viewbox
         * -
         */
        $locations = [];

        foreach ($results as $result) {
            $geoCoderBase = $result->toArray();
            $nominatim = [
                'displayName' => $result->getDisplayName(),
                'category' => $result->getCategory(),
                'type' => $result->getType(),
                'osmId' => $result->getOSMId(),
                'country' => $result->getCountry()->getName(),
                'locality' => $result->getLocality(),
                'sublocality' => $result->getSubLocality(),
            ];
            $locations[] = array_merge($geoCoderBase, $nominatim);
        }
        $location_col = new Collection($locations);

        $location_col = $location_col->groupBy('country');

        $return = $location_col->map(function ($items, $country) use ($location_col) {
            return [
                    'groupType' => 'country',
                    'groupName' => $country,
                    'items' => $items
            ];
        })->values();
        return $return;
    }
}
