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
		
		//require Kohana::find_file('vendor/Transform/lib/Transform', 'Transformer');
		//require Kohana::find_file('vendor/Transform/lib/Transform', 'PropertyManipulator');
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
		
		// Wipe db first?
		if ($clean) $this->_clean_db();
		
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
					"unique":false,
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
					"unique":false,
					"options":{}
				},
				{
					"key":"location_name",
					"label":"Location Name",
					"type":"varchar",
					"input":"text",
					"required":true,
					"unique":false,
					"priority":1
				},
				{
					"key":"location",
					"label":"Location",
					"type":"geometry",
					"input":"text",
					"required":true,
					"unique":false,
					"priority":2
				},
				{
					"key":"verified",
					"label":"Verified",
					"type":"int",
					"input":"checkbox",
					"required":false,
					"unique":false,
					"priority":3
				},
				{
					"key":"source",
					"label":"Source",
					"type":"varchar",
					"input":"select",
					"required":false,
					"unique":false,
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
					"unique":false,
					"priority":0
				},
				{
					"key":"photo",
					"label":"Photo",
					"type":"varchar",
					"input":"file",
					"required":false,
					"unique":false,
					"priority":1
				},
				{
					"key":"video",
					"label":"Video",
					"type":"varchar",
					"input":"file",
					"required":false,
					"unique":false,
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
		
		// Create categories
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
		
		$slugs = array();
		
		// loop to generate slugs to use finding parents
		foreach($categories as $obj)
		{
			$slugs[$obj['category']['id']] = URL::title($obj['category']['title'].'-'.$obj['category']['id']);
		}
		
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
		}
		
		$source = array(
			0 => 'Unknown',
			1 => 'Web',
			2 => 'SMS',
			3 => 'Email',
			4 => 'Twitter'
		);
		
		// FIXME doesn't return incident_person info
		$request = Request::factory("{$url}/index.php/api?task=reports&by=all&comments=1")
			->headers('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
		$response = $request->execute();
		$body = json_decode($response->body(), TRUE);
		
		if (! isset($body['payload']['incidents']))
		{
			throw new Minion_Exception("Error getting incidents. Details:\n\n :error", array(':error' => $response->body()));
		}
		
		$reports = $body['payload']['incidents'];
		
		foreach ($reports as $report)
		{
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
			
			$post_response = Request::factory("api/v2/posts")
			->method(Request::POST)
			->body($body)
			->execute();
			
			$post = json_decode($post_response->body(), TRUE);
			if (! isset($post['id']))
			{
				throw new Minion_Exception("Error creating post. Details:\\nn :error \n\n Request Body: :body", array(
					':error' => $post_response->body(),
					':body' => $body
					));
			}
		}

		$view = View::factory('minion/task/ushahidi/upgrade')
			->set('dry_run', $dry_run)
			->set('quiet', $quiet)
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
