<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Stats for Posts Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Stats_Posts extends Ushahidi_Api {

	/**
	 * @var string oauth2 scope required for access
	 */
	protected $_scope_required = 'stats';

	/**
	 * Load resource object
	 *
	 * @return void
	 */
	protected function _resource()
	{
		parent::_resource();

		$this->_resource = 'stats';
	}

	/**
	 * Get stats for posts
	 *
	 * GET /api/stats/posts
	 *
	 * @return void
	 */

	public function action_get_index_collection()
	{
		$counts = array();

		# Base query
		$base_query = DB::select([DB::expr('COUNT(*)'), 'total']);
		$posts = clone $base_query;
		$posts->from('posts');

		# Filters: 
		# Restrict results to date ranges or form type, if user requests this
		$counts['parameters'] = array();
		$time_step = $this->request->query('timestep');
		if (! empty($time_step))
		{
			$counts['parameters']['timestep'] = $time_step;
		}
		$updated_after = $this->request->query('updated_after');
		if (! empty($updated_after))
		{
			$counts['parameters']['updated_after'] = $updated_after;
			$updated_after = strtotime($updated_after);
			$counts['parameters']['test'] = $updated_after;
			$posts->where('updated', '>', $updated_after);
		}
		$updated_before = $this->request->query('updated_before');
		if (! empty($updated_before))
		{
			$counts['parameters']['updated_before'] = $updated_before;
			$updated_before = strtotime($updated_before);
			$posts->where('updated', '<', $updated_before);
		}
		$form_id = $this->request->query('form_id');
		if (! empty($form_id))
		{
			$counts['parameters']['form_id'] = $form_id;
			$posts->where('form_id', '=', $form_id);
		}
		$formfield_id = $this->request->query('formfield_id');
		$formfield_value = $this->request->query('formfield_value');
		if (!empty($formfield_id) and !empty($formfield_value))
		{
			#count all the values in this formfield
			$counts['parameters']['formfield_id'] = $formfield_id;
			$counts['parameters']['formfield_value'] = $formfield_value;
			$counts['parameters']['formfield_note'] = "No formfield filtering for now";
#			$posts->join('posts_values')
#				->on('posts_values.post_id', '=', 'posts.id');
		}

		#So... we have 2 axes in most of our datasets... examples incude:
		#category totals
		#categories over time, categories over GIS locations, GIS locations over time
		#formfield value totals
		#formfield values over time, formfield values over GIS
		#These will naturally fit different types of map, but we need to be able to set
		#xaxis = time (set bin size), GIS (set GIS source), <formfield values>
		#yaxis = categories, formfield values (set formfield), trusted, none (gives totals)
		#Result will be a sql query giving count of values for xaxis and yaxis combinations
		#e.g. select x,y, count(x) from posts group by x, y
		#for times, will have to round time to nearest hour/day/month etc.

		$axis_query = clone $posts;
		$axis1 = $this->request->query('axis1');
		if (!empty($axis1))
		{
			$counts['parameters']['axis1'] = $axis1;
			$axis1label = FALSE; #probably not necessary, but code crashing... 
			switch ($axis1) {
				case "time":
					#FIXIT: need to adjust this for different time frequency collections
					#e.g. hourly, daily, weekly, monthly, days-of-month, days-of-week, months-of-year etc
					$timestep_label = "DATE";
					if (! empty($time_step))
					{
						switch ($time_step)
						{
							case 'day':
								$timestep_label = "DATE";
								break;
							case 'year':
								$timestep_label = "YEAR";
								break;
							case 'month':
								$timestep_label = "MONTH";
								break;
							default:
						}
					}
					$axis1label = "timespan";
					$axis_query->select([DB::expr($timestep_label.'(FROM_UNIXTIME(`created`))'), $axis1label]);
					break;
				case "form":
					$axis1label = 'formId';
					$axis_query->select([DB::select('form_id'), $axis1label]);
					break;
				case "category":
					$axis1label = 'categoryId';
					$axis_query
						->join('posts_tags')
						->on('posts_tags.post_id', '=', 'posts.id');
					$axis_query->select([DB::select('posts_tags.tag_id'), $axis1label]);
					break;
				case "gis":
					break;
				default:
					#No axes = no outputs
			}

			#Only handle yaxis if an xaxis has been specified
			$axis2 = $this->request->query('axis2');
			$axis2label = FALSE; #probably not necessary, but code crashing... 
			if (!empty($axis2) and ($axis2 <> $axis1))
			{
				$counts['parameters']['axis2'] = $axis2;
				switch($axis2) {
					case "form":
						$axis2label = 'formId';
						$axis_query->select([DB::select('form_id'), $axis2label]);
						break;
					case "category":
						$axis2label = 'categoryId';
						$axis_query
							->join('posts_tags')
							->on('posts_tags.post_id', '=', 'posts.id');
						$axis_query->select([DB::select('posts_tags.tag_id'), $axis2label]);
						break;
					case "gis":
						break;
					default:
							#Default to using axis1 only
				}
			}

			#Default to just axis1 if axis2 is empty or invalid. Don't output if axis1 is empty or invalid.
			if ($axis2label <> FALSE)
			{
				$axis_query->group_by($axis1label, $axis2label);
				$counts['axes'] = $axis_query->execute()->as_array();
			}
			else
			{
				if ($axis1label <> FALSE)
				{
					$axis_query->group_by($axis1label);
					$counts['axes'] = $axis_query->execute()->as_array($axis1label, 'total');
				}
			}
		}

		$this->_response_payload = $counts;
	}

}
