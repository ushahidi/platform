<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Model for Media
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License Version 3 (GPLv3)
 */

class Model_Media extends ORM {

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

			'm_width' => array(
				array('numeric')
			),

			'm_height' => array(
				array('numeric')
			),

			'm_filename' => array(
				array('min_length', array(':value',3)),
				array('max_length', array(':value',100))
			),

			't_width'=> array(
				array('numeric')
			),

			't_height' => array(
				array('numeric')
			),

			't_filename' => array(
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
			$response = array(
				'id' => $this->id,
				'url' => URL::site('api/v'.Ushahidi_Api::version().'/media/'.$this->id, Request::current()),
				'caption' => $this->caption,
				'file_url' => $this->file_url,
				'original_filename' => $this->o_filename,
				'original_width' => $this->o_width,
				'original_height' => $this->o_height,
				'medium_filename' => $this->m_filename,
				'medium_width' => $this->m_width,
				'medium_height' => $this->m_height,
				'thumbnail_filename' => $this->t_filename,
				'thumbnail_width' => $this->t_width,
				'thumbnail_height' => $this->t_height,
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

		if (file_exists($upload_dir.$this->o_filename))
		{
			// Delete the original file
			unlink($upload_dir.$this->o_filename);
		}

		if (file_exists($upload_dir.$this->m_filename))
		{
			// Delete the medium file
			unlink($upload_dir.$this->m_filename);
		}

		if (file_exists($upload_dir.$this->t_filename))
		{
			// Delete the thumbnail file
			unlink($upload_dir.$this->t_filename);
		}

		parent::delete();
	}
}