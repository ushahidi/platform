<?php

/**
 * Ushahidi API Formatter for Media
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;
use Illuminate\Support\Facades\Storage;

class Media extends API
{
    use FormatterAuthorizerMetadata;

    protected function addMetadata(array $data, Entity $media)
    {
        return $data + [
            // Add additional URLs and sizes
            // 'medium_file_url'    => $this->resizedUrl($medium_width, $medium_height, $media->o_filename),
            // 'medium_width'       => $medium_width,
            // 'medium_height'      => $medium_height,
            // 'thumbnail_file_url' => $this->resizedUrl($thumbnail_width, $thumbnail_height, $media->o_filename),
            // 'thumbnail_width'    => $thumbnail_width,
            // 'thumbnail_height'   => $thumbnail_height,

            // Add the allowed HTTP methods
            'allowed_privileges' => $this->getAllowedPrivs($media),
        ];
    }

    protected function getFieldName($field)
    {

        $remap = [
            'o_filename' => 'original_file_url',
            'o_size'     => 'original_file_size',
            'o_width'    => 'original_width',
            'o_height'   => 'original_height',
            ];

        if (isset($remap[$field])) {
            return $remap[$field];
        }

        return parent::getFieldName($field);
    }

    protected function formatOFilename($value)
    {
        // Removes path from image file name, encodes the filename, and joins the path and filename together
        $url_path = explode("/", $value);
        $filename = rawurlencode(array_pop($url_path));
        array_push($url_path, $filename);
        $path = implode("/", $url_path);

        $adapter = Storage::getAdapter();
        // Special handling for RS to get SSL URLs
        if ($adapter instanceof \League\Flysystem\Rackspace\RackspaceAdapter) {
            try {
                return (string) $adapter
                    ->getContainer()
                    ->getObject($path)
                    ->getPublicUrl(\OpenCloud\ObjectStore\Constants\UrlType::SSL);
            } catch (\OpenCloud\ObjectStore\Exception\ObjectNotFoundException $e) {
                return null;
            }
        } else {
            return url(Storage::url($path));
        }
    }
}
