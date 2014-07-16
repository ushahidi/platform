<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Media Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Entity\Media;
use Ushahidi\Entity\MediaRepository;
use Ushahidi\Entity\MediaSearchData;
use Ushahidi\Usecase\Media\CreateMediaRepository;
use Ushahidi\Tool\Uploader;
use Ushahidi\Tool\UploadData;

class Ushahidi_Repository_Media extends Ushahidi_Repository implements
	MediaRepository,
	CreateMediaRepository
{
	private $upload;

	private $created_id;
	private $created_ts;

	public function __construct(Database $db, Uploader $upload)
	{
		parent::__construct($db);

		$this->upload = $upload;
	}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'media';
	}

	// Ushahidi_Repository
	protected function getEntity(Array $data = null)
	{
		return new Media($data);
	}

	// MediaRepository
	public function get($id)
	{
		return $this->getEntity($this->selectOne(compact('id')));
	}

	// MediaRepository
	public function search(MediaSearchData $data, Array $params = null)
	{
		$where = [];
		if ($data->user) {
			$where['user_id'] = $data->user;
		}

		// Start the query, removing empty values
		$query = $this->selectQuery(array_filter($where));

		if (!empty($params['orderby'])) {
			$query->order_by($params['orderby'], Arr::get($params, 'order'));
		}

		if (!empty($params['offset'])) {
			$query->offset($params['offset']);
		}
		if (!empty($params['limit'])) {
			$query->limit($params['limit']);
		}

		if ($data->orphans) {
			$query
				->join('posts_media', 'left')
					->on('posts_media.media_id', '=', 'media.id')
				->where('posts_media.post_id', 'is', NULL);
		}

		$results = $query->execute($this->db);

		return $this->getCollection($results->as_array());
	}

	// CreateMediaRepository
	public function createMedia(Array $file, $caption = null, $user_id = null)
	{
		// Upload the file and get the file reference
		$file = $this->upload->upload(new UploadData($file));

		$input = [
			'mime'       => $file->type,
			'o_filename' => $file->file,
			'o_size'     => $file->size,
			];

		// Add optional fields
		$optional = array_filter(compact('caption', 'user_id') + [
			'o_width'  => $file->width,
			'o_height' => $file->height,
			]);

		if ($optional) {
			$input += $optional;
		}

		$input['created'] = $this->created_ts = time();

		$this->created_id = $this->insert($input);
	}

	// CreateMediaRepository
	public function getCreatedMediaId()
	{
		return $this->created_id;
	}

	// CreateMediaRepository
	public function getCreatedMediaTimestamp()
	{
		return $this->created_ts;
	}

	// CreateMediaRepository
	public function getCreatedMedia()
	{
		return $this->get($this->created_id);
	}
}
