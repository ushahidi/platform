<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Posts Export Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Posts_Export extends Ushahidi_Api {


	/**
	 * @var string Post Type
	 */
	protected $_type = 'report';

	/**
	 * @var string Field to sort results by
	 */
	protected $_record_orderby = 'created';

	/**
	 * @var string Direct to sort results
	 */
	protected $_record_order = 'ASC';

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'posts';

	/**
	 * @var int Maximum number of results to return
	 */
	protected $_record_allowed_orderby = array('id', 'created', 'updated', 'title');

	protected $_parent_id = NULL;

	/**
	 * Load resource object
	 *
	 * @return  void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'export';
	}

	public function action_get_index_collection()
	{

		// Get the post query
		$posts_query = $this->_build_query();

		// Get the count of ALL records
		$count_query = clone $posts_query;
		$total_records = (int) $count_query
			->select(array(DB::expr('COUNT(DISTINCT `post`.`id`)'), 'records_found'))
			->limit(NULL)
			->offset(NULL)
			->find_all()
			->get('records_found');

		// Fetch posts from db
		$posts = $posts_query->find_all();

		// Get query count
		$post_query_sql = $posts_query->last_query();

		// Generate filename using hashed query params and ids
		$filename = 'export-' . hash('sha256', implode('-', $this->request->query()) . '~' . '-' . $this->request->param('id')) . '.csv';;

		// Get existing tsv file
		$tsv_file = Kohana::$config->load('media.media_upload_dir') . $filename;

		// Only generate a new if the file doesn't exist
		if ( !file_exists($tsv_file))
		{

			// Supported headers for the TSV file
			$tsv_headers = array("ID", "PARENT", "USER", "FORM", "TITLE", "CONTENT", "TYPE", "STATUS", "SLUG",
				"LOCALE", "CREATED", "UPDATED", "TAGS", "SETS");

			// Generate tab separated values (tsv)
			$tsv_text = $this->_generate_tsv($tsv_headers,$posts);

			// Write tsv to file
			$this->_write_tsv_to_file($tsv_text, $filename);
		}

		// Relative path
		$relative_path = str_replace( APPPATH.'media'.DIRECTORY_SEPARATOR,'',
			Kohana::$config->load('media.media_upload_dir'));

		// Build download link
		$download_link = URL::site(Media::uri($relative_path . $filename),
			Request::current());

		// Respond with download link and record count
		$this->_response_payload = array(
			'total_count' => $total_records,
			'link' => $download_link
		);
	}

	/**
	 * Generate tab separated values(TSV). Yes not CSV. CSV doesn't seem to
	 * work well with MS Excel when encoded as UTF8.
	 *
	 * @param  array $header    The TSV header
	 * @param  array $posts     The post details for the TSV rows
	 * @return string           The generated TSV string
	 */
	private function _generate_tsv($header, $posts)
	{
		$sep = "\t"; // use tabs as separators. So MS Excel on Windows and Macs can handle UTF data
		$eol = "\r\n"; // line breaks

		$custom_form_values = array();

		$custom_form_unique_headers = array();

		foreach($posts as $post)
		{
			foreach (Model_Form_Attribute::attribute_types() as $type)
			{
				$results = ORM::factory('Post_' . ucfirst($type))
				->where('post_id', '=', $post->id)
				->with('form_attribute')
				->find_all();

				foreach($results as $result)
				{
					if( is_array($result->value))
					{
						// Multi level array.
						// Join parent form attribute key with sub array's form attribute key for the tsv header
						foreach ($result->value as $key => $value)
						{
							$header_value = $result->form_attribute->key . "." .$key;

							$custom_form_values[$post->id][$header_value] = $value;
						}
					}
					else
					{
						$custom_form_values[$post->id][$result->form_attribute->key] = $result->value;
					}
				}
			}
		}

		$custom_form_headers = array();
		foreach ($custom_form_values as $value)
		{
			foreach($value as $key => $val )
			{
				$custom_form_headers[] = $key;
			}
		}

		// Remove duplicate custom forms headers
		$custom_form_unique_headers = array_unique($custom_form_headers);

		// Merge custom form headers and the previously defined headers above
		$header = array_merge($header,$custom_form_unique_headers);

		// Make sure all headers are in upper case
		$header = array_map('strtoupper',$header);

		$tsv =  count($header) ? '"' . implode('"' . $sep . '"', $header) . '"' . $eol : '';

		// Build csv rows
		foreach ($posts as $post)
		{
			$tsv_rows = array();
			$tsv_rows[] = $post->id;
			$tsv_rows[] = $post->parent_id;
			$tsv_rows[] = $post->user_id;
			$tsv_rows[] = $post->form_id;
			$tsv_rows[] = $post->title;
			$tsv_rows[] = $post->content;
			$tsv_rows[] = $post->type;
			$tsv_rows[] = $post->status;
			$tsv_rows[] = $post->slug;
			$tsv_rows[] = $post->locale;
			$tsv_rows[] = $post->created;
			$tsv_rows[] = $post->updated;

			// Make tag values CSV
			$tag_titles = array();

			foreach ($post->tags->find_all() as $tag)
			{
				if ($tag->tag)
				{
					$tag_titles [] = $tag->tag;
				}
			}

			$tsv_rows[] = implode(',',$tag_titles);

			// Make set values CSV
			$set_names = array();

			foreach ($post->sets->find_all() as $set)
			{
				if ($set->name)
				{
					$set_names[] = $set->name;
				}
			}

			$tsv_rows[] = implode(',', $set_names);

			// Custom forms

			foreach($custom_form_values as $key => $values)
			{
				// Make sure the post has custom forms
				if($key == $post->id)
				{

					foreach ($custom_form_unique_headers as $value)
					{
						if(isset($values[$value]))
						{
							$tsv_rows[] = $values[$value];
						}
						else
						{
							$tsv_rows[] = '';
						}
					}
				}
			}

			$format = [$this, '_normalize_empty_value'];

			$tsv_rows = array_map($format, $tsv_rows);

			// Implode as tab separated values
			$tsv .= '"'. implode('"' . $sep . '"', $tsv_rows) . '"' . $eol;
		}

		return chr(255) . chr(254) . mb_convert_encoding($tsv, 'UTF-16LE', 'UTF-8');
	}

	private function _normalize_empty_value($property)
	{
		return (empty($property) OR is_null($property)) ? '' : $property;
	}

	private function _write_tsv_to_file($tsv_text, $filename)
	{
		$tsv_file = Kohana::$config->load('media.media_upload_dir') . $filename;

		$handle = fopen($tsv_file, 'w');

		fwrite($handle, $tsv_text);

		// Free up resources
		fclose($handle);
	}

	// Mostly lifted from the post controller
	private function _build_query()
	{
		$this->_prepare_order_limit_params();

		$posts_query = ORM::factory('Post')
			->distinct(TRUE)
			->where('type', '=', $this->_type)
			->order_by($this->_record_orderby, $this->_record_order);

		// set request
		// set param is set
		$set_id = $this->request->query('set');
		if (! empty($set_id))
		{
			$posts_query->join('posts_sets', 'INNER')
				->on('post.id', '=', 'posts_sets.post_id')
				->where('posts_sets.set_id', '=', $set_id);
		}

		if ($this->_record_limit !== FALSE)
		{
			$posts_query
				->limit($this->_record_limit)
				->offset($this->_record_offset);
		}

		if ($this->_parent_id)
		{
			$posts_query->where('parent_id', '=', $this->_parent_id);
		}

		// Prepare search params
		// @todo generalize this?
		$q = $this->request->query('q');
		if (! empty($q))
		{
			$posts_query->and_where_open();
			$posts_query->where('title', 'LIKE', "%$q%");
			$posts_query->or_where('content', 'LIKE', "%$q%");
			$posts_query->and_where_close();
		}

		$type = $this->request->query('type');
		if (! empty($type))
		{
			$posts_query->where('type', '=', $type);
		}
		$slug = $this->request->query('slug');
		if (! empty($slug))
		{
			$posts_query->where('slug', '=', $slug);
		}
		$form = $this->request->query('form');
		if (! empty($form))
		{
			$posts_query->where('form_id', '=', $form);
		}
		$user = $this->request->query('user');
		if (! empty($user))
		{
			$posts_query->where('user_id', '=', $user);
		}
		$locale = $this->request->query('locale');
		if (! empty($locale))
		{
			$posts_query->where('locale', '=', $locale);
		}
		// Filter on status, default status=published
		$status = $this->request->query('status');
		if (! empty($status))
		{
			if ($status != 'all')
			{
				$posts_query->where('status', '=', $status);
			}
		}
		else
		{
			$posts_query->where('status', '=', 'published');
		}

		// date chcks
		$created_after = $this->request->query('created_after');
		if (! empty($created_after))
		{
			$created_after = date('U', strtotime($created_after));
			$posts_query->where('created', '>=', $created_after);
		}
		$created_before = $this->request->query('created_before');
		if (! empty($created_before))
		{
			$created_before = date('U', strtotime($created_before));
			$posts_query->where('created', '<=', $created_before);
		}
		$updated_after = $this->request->query('updated_after');
		if (! empty($updated_after))
		{
			$updated_after = date('U', strtotime($updated_after));
			$posts_query->where('updated', '>=', $updated_after);
		}
		$updated_before = $this->request->query('updated_before');
		if (! empty($updated_before))
		{
			$updated_before = date('U', strtotime($updated_before));
			$posts_query->where('updated', '<=', $updated_before);
		}

		// Attributes
		// @todo optimize this - maybe iterate over query params instead
		$attributes = ORM::factory('Form_Attribute')->find_all();
		foreach ($attributes as $attr)
		{
			$attr_filter = $this->request->query($attr->key);
			if (! empty($attr_filter))
			{
				$table_name = ORM::factory('Post_'.ucfirst($attr->type))->table_name();
				$sub = DB::select('post_id')
					->from($table_name)
					->where('form_attribute_id', '=', $attr->id)
					->where('value', 'LIKE', "%$attr_filter%");
				$posts_query->join(array($sub, 'Filter_'.ucfirst($attr->key)), 'INNER')->on('post.id', '=', 'Filter_'.ucfirst($attr->key).'.post_id');
			}
		}

		// Filter by tag
		$tags = $this->request->query('tags');
		if (! empty($tags))
		{
			// Default to filtering to ANY of the tags.
			if (! is_array($tags))
			{
				$tags = array('any' => $tags);
			}

			if (isset($tags['any']))
			{
				$tags['any'] = explode(',', $tags['any']);
				$posts_query
					->join('posts_tags')->on('post.id', '=', 'posts_tags.post_id')
					->where('tag_id', 'IN', $tags['any']);
			}

			if (isset($tags['all']))
			{
				$tags['all'] = explode(',', $tags['all']);
				foreach ($tags['all'] as $tag)
				{
					$sub = DB::select('post_id')
						->from('posts_tags')
						->where('tag_id', '=', $tag);

					$posts_query
						->where('post.id', 'IN', $sub);
				}
			}
		}

		return $posts_query;
	}
}