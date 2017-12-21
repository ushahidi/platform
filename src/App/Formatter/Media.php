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

use Kohana;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;

class Media extends API
{
	use FormatterAuthorizerMetadata;

	protected function addMetadata(array $data, Entity $media)
	{
		// Set image dimensions from the config file
		// $medium_width     = Kohana::$config->load('media.image_medium_width');
		// $medium_height    = Kohana::$config->load('media.image_medium_height');
		// $thumbnail_width  = Kohana::$config->load('media.image_thumbnail_width');
		// $thumbnail_height = Kohana::$config->load('media.image_thumbnail_height');

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
		if ($cdnBaseUrl = Kohana::$config->load('cdn.baseurl')) {
			//removes path from image file name, encodes the filename, and joins the path and filename together
			$url_path = explode("/", $value);
			$filename = rawurlencode(array_pop($url_path));
			array_push($url_path, $filename);
			return $cdnBaseUrl . implode("/", $url_path);
		} else {
            // URL::site or Media::uri already encodes the path properly, skip the path wrangling seen above
			return \URL::site(\Media::uri($this->getRelativePath() . $value), \Request::current());
		}
	}

	private function getRelativePath()
	{
		return str_replace(
			Kohana::$config->load('imagefly.source_dir'),
			'',
			Kohana::$config->load('media.media_upload_dir')
		);
	}

	/**
	 * Return URL for accessing the resized image it.
	 *
	 * @param  integer $width    The width of the image
	 * @param  integer $height   The height of the image
	 * @param  string $filename  The file name of the image
	 * @return string           URL to the resized image
	 */
	private function resizedUrl($width, $height, $filename)
	{

		// Format demensions appropriately depending on the value of the height
		if ($height != null) {
			// Image height has been set
			$dimension = sprintf('w%s-h%s', $width, $height);
		} else {
			// No image height set.
			$dimension = sprintf('w%s', $width);
		}

		return \URL::site(
			\Route::get('imagefly')->uri(array(
				'params'    => $dimension,
				'imagepath' => $this->getRelativePath() . $filename,
			)),
			\Request::current()
		);
	}
}
