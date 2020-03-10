<?php

/**
 * Ushahidi Trait to format urls for our rackspace files
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2020 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Illuminate\Support\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Storage;

trait FormatRackspaceURL
{
    
    public function formatUrl($value)
    {
        if (empty($value)) {
            return $value;
        }

        // If we already have a URL, just return it
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Removes path from image file name, encodes the filename, and joins the path and filename together
        $url_path = explode("/", $value);
        $filename = rawurlencode(array_pop($url_path));
        array_push($url_path, $filename);
        $path = implode("/", $url_path);

        $expiration = Carbon::now()->add(CarbonInterval::fromString(config('media.temp_url_lifespan')));

        // Try to get a temporary URL
        try {
            return Storage::temporaryUrl($path, $expiration);
        } catch (\RuntimeException $e) {
            // If it fails (some providers can't support it) fallback to a standard URL
            return url(Storage::url($path));
        } catch (\OpenCloud\ObjectStore\Exception\ObjectNotFoundException $e) {
            // Catch ObjectNotFoundException from Rackspace
            return null;
        }
    }
}
