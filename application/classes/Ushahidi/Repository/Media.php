<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Media Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Media;
use Ushahidi\Core\Usecase\Layer\LayerMediaRepository;
use Ushahidi\Core\Tool\Uploader;
use Ushahidi\Core\Tool\UploadData;

class Ushahidi_Repository_Media extends Ushahidi_Repository implements
	LayerMediaRepository
{
	private $upload;

	private $created_id;
	private $created_ts;

	private $deleted_media;

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
	public function getEntity(Array $data = null)
	{
		return new Media($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['user', 'orphans'];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		if ($search->user) {
			$this->search_query->where('user_id', '=', $search->user);
		}

		if ($search->orphans) {
			$this->search_query
				->join('posts_media', 'left')
					->on('posts_media.media_id', '=', 'media.id')
				->where('posts_media.post_id', 'is', NULL);
		}
	}

	// LayerMediaRepository
	public function doesMediaExist($id)
	{
		return $this->selectCount(compact('id')) !== 0;
	}
}
