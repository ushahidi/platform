<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Displays the current status of migrations in all groups
 *
 * This task takes no config options
 *
 */
class Task_Ushahidi_Upgrade extends Minion_Task {

	/**
	 * A set of config options that this task accepts
	 * @var array
	 */
	protected $_options = array(
		'dry-run'     => FALSE,
		'quiet'       => FALSE,
		'verbose'     => FALSE,
		'clean'       => FALSE,
		'url'         => FALSE,
		'type'        => FALSE,
		'username'    => FALSE,
		'password'    => FALSE,
	);

// @todo add build validation to validate options

	protected function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the task
	 *
	 * @param array $options Config for the task
	 */
	protected function _execute(array $options)
	{
		$dry_run  = $options['dry-run'] !== FALSE;
		$quiet    = $options['quiet'] !== FALSE;
		$verbose  = $options['verbose'] !== FALSE;
		$clean    = $options['clean'] !== FALSE;
		
		$url      = $options['url']; // URL
		$username = $options['username'];
		$password = $options['password'];
		$type     = $options['type'];   // api / sql
		
		$post_count = 0;
		$category_count = 0;
		
		ob_end_flush();
		ob_implicit_flush(TRUE);
		ini_set('memory_limit', -1); // Disable memory limit
		
		// Wipe db first?
		if ($clean)
		{
			echo "Cleaning DB\n\n";
			$this->_clean_db();
		}
		
		// Hack so URL::site() doesn't error out
		if (Kohana::$base_url == '/') $_SERVER['SERVER_NAME'] = 'internal';
		
		// Create 2.x style reports form
		$form_data = <<<EOFORM
{
	"name":"Classic Report Form",
	"type":"report",
	"description":"Classic Ushahidi 2.x Report Form",
	"groups":[
		{
			"label":"Incident Fields",
			"priority": 1,
			"attributes":[
				{
					"key":"original_id",
					"label":"Original ID",
					"type":"int",
					"input":"text",
					"required":false,
					"priority":0,
					"default":"",
					"options":{}
				},
				{
					"key":"date",
					"label":"Date",
					"type":"datetime",
					"input":"date",
					"required":true,
					"priority":0,
					"default":"",
					"options":{}
				},
				{
					"key":"location_name",
					"label":"Location Name",
					"type":"varchar",
					"input":"text",
					"required":true,
					"priority":1
				},
				{
					"key":"location",
					"label":"Location",
					"type":"geometry",
					"input":"text",
					"required":true,
					"priority":2
				},
				{
					"key":"verified",
					"label":"Verified",
					"type":"int",
					"input":"checkbox",
					"required":false,
					"priority":3
				},
				{
					"key":"source",
					"label":"Source",
					"type":"varchar",
					"input":"select",
					"required":false,
					"priority":4,
					"default":"Web",
					"options":[
						"Unknown",
						"Web",
						"SMS",
						"Email",
						"Twitter"
					]
				}
			]
		},
		{
			"label":"Media Fields",
			"priority": 2,
			"attributes":[
				{
					"key":"news",
					"label":"News",
					"type":"varchar",
					"input":"text",
					"required":false,
					"priority":0
				},
				{
					"key":"photo",
					"label":"Photo",
					"type":"varchar",
					"input":"file",
					"required":false,
					"priority":1
				},
				{
					"key":"video",
					"label":"Video",
					"type":"varchar",
					"input":"file",
					"required":false,
					"priority":2
				}
			]
		}
	]
}
EOFORM;

		$form_response = Request::factory("api/v2/forms")
			->method(Request::POST)
			->body($form_data)
			->execute();
		$form_body = json_decode($form_response->body(), TRUE);
		
		if (! isset($form_body['id']))
		{
			throw new Minion_Exception("Error creating form. Details:\n :error", array(':error' => $form_response->body()));
		}
		
		$form_id = $form_body['id'];
		echo "Created form id: $form_id\n\n";
		
		// Create categories
		echo "Fetching categories\n";
		// FIXME will only get visible categories. Probably just have to caveat with that
		$cat_request = Request::factory("{$url}/index.php/api?task=categories")
			->headers('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
		$cat_response = $cat_request->execute();
		$cat_body = json_decode($cat_response->body(), TRUE);
		
		if (! isset($cat_body['payload']['categories']))
		{
			throw new Minion_Exception("Error getting categories. Details:\n :error", array(':error' => $cat_response->body()));
		}
		
		$categories = $cat_body['payload']['categories'];
		
		unset($form_response, $form_body, $cat_request, $cat_response, $cat_body);
		
		$slugs = array();
		
		// loop to generate slugs to use finding parents
		foreach($categories as $obj)
		{
			$slugs[$obj['category']['id']] = URL::title($obj['category']['title'].'-'.$obj['category']['id']);
		}
		
		echo "Importing categories\n";
		foreach($categories as $obj)
		{
			$category = $obj['category'];
			// FIXME nowhere to store color/icon/description/translations
			$body = json_encode(array(
				"tag" => $category['title'],
				"type" => "category",
				"slug" => URL::title($category['title'].'-'.$category['id']), // Hack to handle dupe titles
				"priority" => $category['position'],
				"parent" => isset($slugs[$category['parent_id']]) ? $slugs[$category['parent_id']] : 0
			));
			$tag_response = Request::factory("api/v2/tags")
			->method(Request::POST)
			->body($body)
			->execute();
			
			$tag = json_decode($tag_response->body(), TRUE);
			if (! isset($tag['id']))
			{
				throw new Minion_Exception("Error creating tag. Details:\n\n :error \n\n Request Body: :body", array(
					':error' => $tag_response->body(),
					':body' => $body
					));
			}
			
			$category_count++;
			
			unset($tag_response, $tag, $body, $category);
		}
		
		unset($categories);
		
		$source = array(
			0 => 'Unknown',
			1 => 'Web',
			2 => 'SMS',
			3 => 'Email',
			4 => 'Twitter'
		);
		
		// TODO make batch size an option
		$limit = 20;
		$since = 0;
		$done = FALSE;
		flush();
		while (! $done)
		{
			echo "Memory Usage: ". memory_get_usage() . "\n";
			echo "Fetching reports $limit reports, starting at ID: $since\n";
			$processed = 0;
			
			// FIXME doesn't return incident_person info
			// FIXME can't get unapproved reports
			$request = Request::factory("{$url}/index.php/api?task=incidents&by=sinceid&comments=1&sort=ASC&orderfield=incidentid&id={$since}&limit={$limit}")
				->headers('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
			$response = $request->execute();
			$body = json_decode($response->body(), TRUE);
			
			if (! isset($body['payload']['incidents']))
			{
				throw new Minion_Exception("Error getting incidents. Details:\n\n :error", array(':error' => $response->body()));
			}
			
			$reports = $body['payload']['incidents'];
			
			unset($request, $response, $body);
			
			foreach ($reports as $report)
			{
				echo "Memory Usage: ". memory_get_usage() . "\n";
				echo "Importing report id: ".$report['incident']['incidentid']." .. ";
				$incident = $report['incident'];
				$categories = $report['categories'];
				$media = $report['media'];
				$comments = $report['comments'];
				$customfields = $report['customfields'];
				
				$tags = array();
				foreach ($categories as $cat)
				{
					if (isset($slugs[$cat['category']['id']]))
					{
						$tags[] = $slugs[$cat['category']['id']];
					}
				}
				
				$body = json_encode(array(
					"form" => $form_id,
					"title" => $incident['incidenttitle'],
					"content" => $incident['incidentdescription'],
					"author" => "",
					"email" => "",
					"type" => "report",
					"status" => $incident['incidentactive'] ? 'published' : 'draft',
					"locale" => "en_US",
					"values" => array(
						"original_id" => $incident['incidentid'],
						"date" => $incident['incidentdate'],
						"location_name" => $incident['locationname'],
						"location" => "",
						"verified" => $incident['incidentverified'],
						"source" => $source[$incident['incidentmode']],
						// FIXME
						"news" => "",
						"photo" => "",
						"video" => "",
					),
					"tags" => $tags
				));
				
				echo "POSTing.. "; echo $body;
				$post_response = Request::factory("http://ushv3.dev/api/v2/posts")
				->method(Request::POST)
				->body($body)
				->execute();
				
				if ($post_response->status() != 200)
				{
					throw new Minion_Exception("Error creating post. Server returned :status. Details:\\nn :error \n\n Request Body: :body", array(
						':status' => $post_response->status(),
						':error' => $post_response->body(),
						':body' => $body
						));
				}
				
				$post = json_decode($post_response->body(), TRUE);
				if (! isset($post['id']))
				{
					throw new Minion_Exception("Error creating post. Details:\\nn :error \n\n Request Body: :body", array(
						':error' => $post_response->body(),
						':body' => $body
						));
				}

				echo " .. new post id: {$post['id']}\n";

				// Set start id for next batch
				$since = $incident['incidentid'];

				unset($post_response, $post, $body, $tags, $incident, $categories, $media, $comments, $customfields);

				$processed++;
				$post_count++;
			}

			unset($reports);

			if ($processed == 0) $done = TRUE;
		}

		$view = View::factory('minion/task/ushahidi/upgrade')
			->set('dry_run', $dry_run)
			->set('quiet', $quiet)
			->set('form_id', $form_id)
			->set('post_count', $post_count)
			->set('category_count', $category_count)
			->set('memory_used', memory_get_peak_usage())
			->set('dry_run_sql', array());

		echo $view;
	}

	protected static function _clean_db()
	{
		DB::query(Database::UPDATE, "SET FOREIGN_KEY_CHECKS=0;")->execute();
		// Forms, Attributes, Groups
		DB::query(Database::DELETE, "TRUNCATE TABLE forms")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_groups")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_attributes")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_groups_form_attributes")->execute();
		// Posts & field values
		DB::query(Database::DELETE, "TRUNCATE TABLE posts")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_datetime")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_decimal")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_geometry")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_int")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_point")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_text")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE post_varchar")->execute();
		// Tags
		DB::query(Database::DELETE, "TRUNCATE TABLE tags")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE posts_tags")->execute();
		// Sets
		DB::query(Database::DELETE, "TRUNCATE TABLE sets")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE posts_sets")->execute();
		
		DB::query(Database::UPDATE, "SET FOREIGN_KEY_CHECKS=1;")->execute();
	}

}
