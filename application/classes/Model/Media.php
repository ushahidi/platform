<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model for Media
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Model_Media extends ORM implements Acl_Resource_Interface {
	/**
	 * A media has and belongs to many posts
	 *
	 * @var array Relationships
	 */
	protected $_has_many = array(
		'posts' => array('through' => 'posts_media'),
		);

	/**
	 * A set belongs to a user
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = array(
		'user' => array()
		);

	/**
	 * Rules for the media model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'id' => array(
				array('numeric')
			),

			'user_id' => array(
				array('numeric'),
			),

			'o_width' => array(
				array('numeric')
			),

			'o_height' => array(
				array('numeric')
			),

			'o_filename' => array(
				array('min_length', array(':value',3)),
				array('max_length', array(':value',100))
			),

			// Caption set on meda
			'caption' => array(
				array('min_length', array(':value', 3)),
				array('max_length', array(':value', 255))
			)
		);

	}

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);
	protected $_updated_column = array('column' => 'updated', 'format' => TRUE);
	
	/**
	 * Prepare media data for API
	 *
	 * @return array $response - array to be returned by API (as json)
	 */
	public function for_api()
	{
		$response = array();

		if ($this->loaded())
		{
			// Set image dimensions from the config file
			$medium_width = 	Kohana::$config->load('media.image_medium_width');
			$medium_height = Kohana::$config->load('media.image_medium_height');

			$thumbnail_width = Kohana::$config->load('media.image_thumbnail_width');
			$thumbnail_height = Kohana::$config->load('media.image_thumbnail_height');

			$upload_path = Kohana::$config->load('media.media_upload_dir');
			$relative_path = str_replace(Kohana::$config->load('imagefly.media_dir'),'',Kohana::$config->load('media.media_upload_dir'));

			$original_file = $upload_path . $this->o_filename;

			$response = array(
				'id' => $this->id,
				'user' => empty($this->user_id) ? NULL : array(
					'id' => $this->user_id,
					'url' => Ushahidi_Api::url('users', $this->user_id)
				),
				'url' => Ushahidi_Api::url('media', $this->id),
				'caption' => $this->caption,
				'mime' => $this->mime,
				'original_file_url' => URL::site(Media::uri($relative_path.$this->o_filename), Request::current()),
				'original_file_size' => is_file($original_file) ? filesize($upload_path.$this->o_filename) : 0,
				'original_width' => $this->o_width,
				'original_height' => $this->o_height,
				'medium_file_url' => $this->_resized_url($medium_width, $medium_height, $relative_path.$this->o_filename),
				'medium_width' => $medium_width,
				'medium_height' => $medium_height,
				'thumbnail_file_url' => $this->_resized_url($thumbnail_width, $thumbnail_height, $relative_path.$this->o_filename),
				'thumbnail_width' => $thumbnail_width,
				'thumbnail_height' => $thumbnail_height,
				'created' => ($created = DateTime::createFromFormat('U', $this->created))
					? $created->format(DateTime::W3C)
					: $this->created,
				'updated' => ($updated = DateTime::createFromFormat('U', $this->updated))
					? $updated->format(DateTime::W3C)
					: $this->updated,
			);
		}
		else
		{
			$response = array(
				'errors' => array(
					'Media does not exist'
				)
			);
		}

		return $response;
	}

	public function delete()
	{

		$upload_dir = Kohana::$config->load('media.media_upload_dir');

		// Delete files from disk
		try
		{
			if (file_exists($upload_dir.$this->o_filename))
			{
				// Delete the original file
				 unlink($upload_dir.$this->o_filename);

			}
		}
		catch (ErrorException $e)
		{
			// Catch any delete file warnings
			if ($e->getCode() === E_WARNING)
			{
				// Log warning to log file.
				Kohana::$log->add(Log::WARNING, 'Cannot delete file: :message',
				array(':message' => $e->getMessage()));
			}
			else
			{
				throw $e;
			}
		}

		// Delete database entry
		parent::delete();
	}

	/**
	 * Return URL for accessing the resized image it.
	 *
	 * @param  integer $width    The width of the image
	 * @param  integer $height   The height of the image
	 * @param  string $filename  The file name of the image
	 * @return string           URL to the resized image
	 */
	private function _resized_url($width, $height, $filename)
	{
		// Format demensions appropriately depending on the value of the height
		if ($height != NULL)
		{
			// Image height has been set
			$dimension = sprintf('w%s-h%s', $width, $height);
		}
		else
		{
			// No image height set.
			$dimension = sprintf('w%s', $width);
		}

		return URL::site(
			Route::get('imagefly')->uri(array(
				'params' => $dimension,
				'imagepath' => $filename
			)),
			Request::current()
		);
	}

	/**
	 * Returns the string identifier of the Resource
	 *
	 * @return string
	 */
	public function get_resource_id()
	{
		return 'media';
	}
}