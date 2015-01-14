<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Ushahidi 2.x Import Task
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Tasks
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

/**
 * Imports data from Ushahidi 2.x
 *
 * Examples:
 *
 *   Import from API:
 *
 *     ./minion --task=ushahidi:import2x --source=api --url=http://zombiereports.com
 *
 *   Import from DB:
 *
 *     ./minion --task=ushahidi:import2x --source=sql --database=ushahidi_zombie --username=ushahidi --password=ushahidi
 *
 * Available config options are:
 *
 * --source=source
 *
 *   The type of source to import from: api or sql
 *
 * --url=url
 *
 *   Specify the base url of a 2.x deployment to import from
 *
 * --username=username
 * --password=password
 *
 *   The username and password of a superadmin user on the 2.x deployment (if importing via API)
 *   Or the username and password for the DB being imported.
 *
 * --database=database
 *
 *   Specify the database name of a 2.x deployment to import from
 *
 * --hostname=hostname
 *
 *   Specify the mysql hostname to import from
 *
 * --dest-username=username
 * --dest-password=password
 *
 *   Username and password of an admin user on the V3 deployment we are importing into
 *
 * --oauth-client-id=id
 * --oauth-client-secret=secret
 *
 *   OAuth client details for the V3 deployment we are importing into
 *
 * --form-id=id
 *
 *   Specify existing form ID to use for imported posts
 *
 * --clean
 *
 *   Clean the 3.x DB before importing. This will wipe any existing data.
 *   This option is not compatible with use_externl=url (although can be used with --use_external)
 *
 * --proxy=proxy
 *
 *   A proxy server to user for any external connections
 *
 * --use_external=[url]
 *
 *   Use external HTTP requests to connect to the 3.x API.
 *
 *   You can optionally pass a base URL here, otherwise the base_url
 *   defined in bootstrap.php will be used.
 *
 * --quiet
 *
 *  Suppress all unnecessary output.
 *
 * --verbose
 *
 *  Display full details of posts being imported.
 *
 * --debug
 *
 *  Display additional debug info. Implies --verbose
 *
 */
class Task_Ushahidi_Import2x extends Minion_Task {

	/**
	 * A set of config options that this task accepts
	 * @var array
	 */
	protected $_options = array(
		'quiet'       => FALSE,
		'verbose'     => FALSE,
		'debug'       => FALSE,
		'clean'       => FALSE,
		'use-external'       => FALSE,
		'url'         => FALSE,
		'source'      => FALSE,
		'hostname'    => FALSE,
		'database'    => FALSE,
		'username'    => FALSE,
		'password'    => "",
		'dest-username'      => FALSE,
		'dest-password'      => "",
		'oauth-client-id'    => FALSE,
		'oauth-client-secret'=> FALSE,
		'proxy'       => FALSE,
		'batch-size'  => FALSE,
		'form-id'     => FALSE
	);

	/**
	 * Proxy url
	 * @param String
	 */
	protected $proxy = FALSE;

	/**
	 * Base URL of 3.x deployment or TRUE
	 * @param String|Boolean
	 */
	protected $use_external = FALSE;

	/**
	 * Size of batch when importing reports
	 * @param Int|Boolean
	 */
	protected $batch_size = 20;

	/**
	 * OAuth Access token for V3 destination
	 * @param String|Boolean
	 */
	protected $oauth_token = FALSE;

	/**
	 * Log instance
	 * @param Log
	 */
	protected $logger;

	/**
	 * Database instance for Ushahidi 2.x DB
	 * @param Database
	 */
	protected $db2;

	protected function __construct()
	{
		parent::__construct();

		// Enable immediate log writing
		Log::$write_on_add = TRUE;
		// Create new Log instance, don't use default one otherwise we pollute the logs
		$this->logger = new Log;
	}

	/**
	 * Add extra parameter validation
	 */
	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('source', 'not_empty')
			->rule('source', 'in_array', array(':value', array('api', 'sql')) )
			->rule('url', 'url')
			->rule('url', function($validation, $field, $value, $data) {
					if ($data['source'] == 'api' AND ! Valid::not_empty($value))
					{
						$validation->error($field, 'not_empty');
					}
				},
				array(':validation', ':field', ':value', ':data'))
			// Ensure use_external is either TRUE or a valid url
			->rule('use-external', function($validation, $field, $value, $data)
				{
						if ($value === TRUE) return TRUE;

						if (is_string($value) AND ! Valid::url($value))
						{
							$validation->error($field, 'url');
						}

						return FALSE;
				},
				array(':validation', ':field', ':value', ':data'))
			->rule('username', function($validation, $field, $value, $data)
				{
					if ($data['source'] == 'sql' AND ! Valid::not_empty($value))
					{
						$validation->error($field, 'not_empty');
					}
				},
				array(':validation', ':field', ':value', ':data'))
			->rule('database', function($validation, $field, $value, $data)
				{
					if ($data['source'] == 'sql' AND ! Valid::not_empty($value))
					{
						$validation->error($field, 'not_empty');
					}
				},
				array(':validation', ':field', ':value', ':data'))
			// Reject clean if also using external url
			->rule('clean', function($validation, $field, $value, $data) {
					if ($value !== FALSE AND is_string($data['use-external']))
					{
						$validation->error($field, 'incompatible_use_external');
					}
				},
				array(':validation', ':field', ':value', ':data'))
			->rule('batch-size', 'numeric')
			->rule('form-id', 'numeric')
			->rule('oauth-client-id', 'not_empty')
			->rule('oauth-client-secret', 'not_empty')
			->rule('dest-username', 'not_empty');
	}

	/**
	 * Execute the task
	 *
	 * @param array $options Config for the task
	 */
	protected function _execute(array $options)
	{
		$post_count = $category_count = $user_count = 0;

		ini_set('memory_limit', -1); // Disable memory limit

		// Hack so URL::site() doesn't error out
		if (Kohana::$base_url == '/') $_SERVER['SERVER_NAME'] = 'internal';

		// Get CLI params
		$quiet          = $options['quiet'] !== FALSE;
		$debug          = $options['debug'] !== FALSE;
		$verbose        = $options['verbose'] !== FALSE;

		$clean          = $options['clean'] !== FALSE;
		$use_external   = $options['use-external'];
		$this->proxy    = $options['proxy'];
		$batch_size     = $options['batch-size'];
		$form_id        = $options['form-id'];

		$source         = $options['source'];
		$url            = $options['url'];
		$username       = $options['username'];
		$password       = $options['password'];
		$database       = $options['database'];
		$hostname       = $options['hostname'];

		// V3 oauth creds
		$oauth_client_id      = $options['oauth-client-id'];
		$oauth_client_secret  = $options['oauth-client-secret'];
		$dest_username        = $options['dest-username'];
		$dest_password        = $options['dest-password'];

		// Check log levels based on quiet/verbose/debug option
		if ($debug)
		{
			$max_level = Log::DEBUG;
		}
		elseif ($verbose)
		{
			$max_level = Log::INFO;
		}
		elseif ($quiet)
		{
			$max_level = Log::WARNING;
		}
		else
		{
			$max_level = Log::NOTICE;
		}
		// Attach StdOut logger
		$this->logger->attach(new Log_StdOut, $max_level, 0, TRUE);

		// Handle 'use_external' param
		if (is_string($use_external))
		{
			$this->use_external = $use_external;
		}
		elseif ($use_external !== FALSE)
		{
			if (Kohana::$base_url == '/') throw new Minion_Exception('To use --use_external : Set base_url in bootstrap, or pass a base url');

			$this->use_external = Kohana::$base_url;
		}
		// Ensure base url ends with /
		if ($this->use_external AND substr_compare($this->use_external, '/', -1) !== 0) $this->use_external .= '/';

		// Default to localhost if no hostname provided
		if (! $hostname) $hostname = "127.0.0.1";

		// Save batch size if passed
		if (intval($batch_size) > 0) $this->batch_size = intval($batch_size);

		// Wipe db first?
		if ($clean)
		{
			$this->logger->add(Log::NOTICE, 'Cleaning DB');
			$this->_clean_db($dest_username);
		}

		// OAuth juggling
		$response = $this->_request('oauth/token')
			->method(Request::POST)
			->post(array(
				'grant_type'    => 'password',
				'client_id'     => $oauth_client_id,
				'client_secret' => $oauth_client_secret,
				'username'      => $dest_username,
				'password'      => $dest_password,
				'scope'         => 'api posts forms tags sets users',
			))
			->execute();
		$body = json_decode($response->body(), TRUE);
		if ($response->status() != 200 OR ! isset($body['access_token']))
		{
			throw new Minion_Exception("Error getting oauth token. Details:\n\n HTTP Status: :status, Body: :error",
				array(
					':status' => $response->status(),
					':error' => $response->body()
				)
			);
		}
		$this->oauth_token = $body['access_token'];

		// Create 2.x style form if --form-id param not passed
		if (! $form_id)
		{
			$form_id = $this->_create_form();
		}

		if($source == 'sql')
		{
			$this->db2 = Database::instance('ushahidi2', array
				(
					'type'       => 'MySQLi',
					'connection' => array(
						'hostname'   => $hostname,
						'database'   => $database,
						'username'   => $username,
						'password'   => $password,
						'persistent' => FALSE,
					),
					'table_prefix' => '',
					'charset'      => 'utf8',
					'caching'      => FALSE,
					'profiling'    => TRUE,
				)
			);
			$this->db2->connect();

			$category_count = $this->_sql_import_categories();

			$user_count = $this->_sql_import_users();

			$post_count = $this->_sql_import_reports($form_id);
		}
		elseif ($source == 'api')
		{
			// Ensure url ends with /
			$url = rtrim($url, '/') . '/';

			$request = $this->_request("{$url}index.php/api?task=version")
				->headers('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
			$response = $request->execute();
			$body = json_decode($response->body(), TRUE);

			if ($response->status() != 200 OR ! isset($body['payload']['version'][0]['version']))
			{
				throw new Minion_Exception("Could not connect to 2.x API. Details:\n\n HTTP Status: :status, Body: :error",
					array(
						':status' => $response->status(),
						':error' => $response->body()
					)
				);
			}

			$this->logger->add(Log::NOTICE, 'Successfully connected to API. Remote Ushahidi version: :version', array(':version' => $body['payload']['version'][0]['version']));

			$category_count = $this->_api_import_categories($url, $username, $password);

			$post_count = $this->_api_import_reports($form_id, $url, $username, $password);

			// @todo import users, needs 2.x API first
		}

		$view = View::factory('minion/task/ushahidi/import2x')
			->set('quiet', $quiet)
			->set('debug', $debug)
			->set('verbose', $verbose)
			->set('form_id', $form_id)
			->set('post_count', $post_count)
			->set('category_count', $category_count)
			->set('user_count', $user_count)
			->set('memory_used', memory_get_peak_usage());

		echo $view;

		if (Kohana::$profiling)
		{
			//echo View::factory('profiler/stats');
		}
	}

	/**
	 * Empty DB tables
	 */
	protected function _clean_db($dest_username)
	{
		DB::query(Database::UPDATE, "SET FOREIGN_KEY_CHECKS=0;")->execute();
		// Forms, Attributes, Groups
		DB::query(Database::DELETE, "TRUNCATE TABLE forms")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_groups")->execute();
		DB::query(Database::DELETE, "TRUNCATE TABLE form_attributes")->execute();
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
		// Users
		DB::query(Database::DELETE, "DELETE FROM users where username <> :username OR USERNAME IS NULL")->bind(':username', $dest_username)->execute();

		DB::query(Database::UPDATE, "SET FOREIGN_KEY_CHECKS=1;")->execute();
	}

	/**
	 * Request Factory wrapper to handle proxy settings
	 */
	protected function _request($location)
	{
		$V3 = FALSE;
		if (stripos($location, 'http') === FALSE)
		{
			$V3 = TRUE;
		}

		if ($this->use_external AND $V3)
		{
			$location = $this->use_external.$location;
		}

		$request = Request::factory($location);

		if ($V3)
		{
			// add oauth token
			$request->headers('Authorization', 'Bearer ' . $this->oauth_token);
		}

		if ($this->proxy AND $request->client() instanceof Request_Client_External)
		{
			$request->client()->options(CURLOPT_PROXY, $this->proxy);
		}

		return $request;
	}

	/**
	 * Import reports
	 *
	 * @param int $form_id      Form ID for 2.x style form
	 * @param string $url       Base url of deployment to import from
	 * @param string $username  Username on 2.x deploymnet
	 * @param string $password  Password on 2.x deployment
	 * @return int count of report imported
	 */
	protected function _api_import_reports($form_id, $url, $username, $password)
	{
		$post_count = 0;
		$source = array(
			0 => 'Unknown',
			1 => 'Web',
			2 => 'SMS',
			3 => 'Email',
			4 => 'Twitter'
		);

		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Upgrade', __FUNCTION__);
		}

		$limit = $this->batch_size;
		$since = 0;
		$done = FALSE;

		while (! $done)
		{
			$this->logger->add(Log::DEBUG, 'Memory Usage: :mem', array(':mem' => memory_get_usage()));
			$this->logger->add(Log::NOTICE, 'Fetching reports :limit reports, starting at ID: :since',
				array(':limit' => $limit, ':since' => $since));
			$processed = 0;

			// FIXME doesn't return incident_person info
			// FIXME can't get unapproved reports
			$request = $this->_request("{$url}index.php/api?task=incidents&by=sinceid&comments=1&sort=0&orderfield=incidentid&id={$since}&limit={$limit}")
				->headers('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
			$response = $request->execute();
			$body = json_decode($response->body(), TRUE);

			if (! isset($body['payload']['incidents']) OR $response->status() != 200)
			{
				// Check for 'No data' error
				if (isset($body['payload']['success']) AND $body['payload']['success'] == "true"
					AND isset($body['error']['code']) AND $body['error']['code'] == 007 )
				{
					$done = TRUE; continue;
				}

				throw new Minion_Exception("Error getting incidents. Details:\n\n HTTP Status: :status, Body: :error",
					array(
						':status' => $response->status(),
						':error' => $response->body()
					)
				);
			}

			$reports = $body['payload']['incidents'];

			unset($request, $response, $body);

			foreach ($reports as $report)
			{
				$this->logger->add(Log::DEBUG, 'Memory Usage: :mem', array(':mem' => memory_get_usage()));
				$this->logger->add(Log::INFO, 'Importing report id:  :id', array(':id' => $report['incident']['incidentid']));

				$incident = isset($report['incident']) ? $report['incident'] : array();
				$categories = isset($report['categories']) ? $report['categories'] : array();
				$media = isset($report['media']) ? $report['media'] : array();
				$comments = isset($report['comments']) ? $report['comments'] : array();
				$customfields = isset($report['customfields']) ? $report['customfields'] : array();

				$tags = array();
				foreach ($categories as $cat)
				{
					if (isset($this->tag_map[$cat['category']['id']]))
					{
						$tags[$this->tag_map[$cat['category']['id']]] = $this->tag_map[$cat['category']['id']];
					}
				}

				$news_media = $video_media = $photo_media = array();
				foreach($media as $m)
				{
					switch ($m['type'])
					{
						// photo
						case 1:
							break;
						// video
						case 2:
							$news_media[] = array(
								'value' => $m['link']
							);
							break;
						// news
						case 4:
							$news_media[] = array(
								'value' => $m['link']
							);
							break;
					}
				}

				$body = json_encode(array(
					"form" => $form_id,
					"title" => substr($incident['incidenttitle'], 0, 150), // Make we don't exceed the max length.
					"content" => $incident['incidentdescription'],
					//"author" => "",
					//"email" => "",
					"type" => "report",
					"status" => $incident['incidentactive'] ? 'published' : 'draft',
					"locale" => "en_US",
					"values" => array(
						"original_id" => $incident['incidentid'],
						"date" => $incident['incidentdate'],
						"location_name" => $incident['locationname'],
						"location" => (is_numeric($incident['locationlatitude']) AND is_numeric($incident['locationlongitude'])) ? array(
							'lat' => $incident['locationlatitude'],
							'lon' => $incident['locationlongitude']
						) : NULL,
						"verified" => $incident['incidentverified'],
						"source" => $source[$incident['incidentmode']],
						"news" => $news_media,
						"photo" => $photo_media,
						"video" => $video_media,
					),
					"tags" => $tags
				));

				$this->logger->add(Log::DEBUG, "POSTing..\n :body", array(':body' => $body));
				$post_response = $this->_request("api/v2/posts")
				->method(Request::POST)
				->body($body)
				->execute();

				if ($post_response->status() != 200)
				{
					throw new Minion_Exception("Error creating post. Server returned :status. Details:\n\n :error \n\n Request Body: :body", array(
						':status' => $post_response->status(),
						':error' => $post_response->body(),
						':body' => $body
						));
				}

				$post = json_decode($post_response->body(), TRUE);
				if (! isset($post['id']))
				{
					throw new Minion_Exception("Error creating post. Details:\n\n :error \n\n Request Body: :body", array(
						':error' => $post_response->body(),
						':body' => $body
						));
				}

				$this->logger->add(Log::INFO, "new post id: :id", array(':id' => $post['id']));

				// Set start id for next batch
				$since = $incident['incidentid'];

				unset($post_response, $post, $body, $tags, $incident, $categories, $media, $comments, $customfields);

				$processed++;
				$post_count++;
			}

			unset($reports);

			if ($processed == 0) $done = TRUE;
		}

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $post_count;
	}

	/**
	 * Import reports
	 *
	 * @param int $form_id      Form ID for 2.x style form
	 * @param string $url       Base url of deployment to import from
	 * @param string $username  Username on 2.x deploymnet
	 * @param string $password  Password on 2.x deployment
	 * @return int count of report imported
	 */
	protected function _sql_import_reports($form_id)
	{
		$post_count = 0;
		$source = array(
			0 => 'Unknown',
			1 => 'Web',
			2 => 'SMS',
			3 => 'Email',
			4 => 'Twitter'
		);

		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Upgrade', __FUNCTION__);
		}

		$limit = $this->batch_size;
		$offset = 0;
		$done = FALSE;

		while (! $done)
		{
			$this->logger->add(Log::DEBUG, 'Memory Usage: :mem', array(':mem' => memory_get_usage()));
			$this->logger->add(Log::NOTICE, 'Fetching reports reports :offset - :end',
				array(':offset' => $offset, ':end' => $offset+$limit ));
			$processed = 0;

			$reports = DB::query(Database::SELECT, '
					SELECT *, i.id AS incident_id FROM incident i
					LEFT JOIN incident_person p ON (i.id = p.incident_id)
					LEFT JOIN location l ON (i.location_id = l.id)
					LEFT JOIN users u ON (i.user_id = u.id)
					ORDER BY i.id ASC LIMIT :limit OFFSET :offset
				')
				->parameters(array(':limit' => $limit, ':offset' => $offset))
				->execute($this->db2);

			foreach ($reports as $report)
			{
				$this->logger->add(Log::DEBUG, 'Memory Usage: :mem', array(':mem' => memory_get_usage()));
				$this->logger->add(Log::INFO, 'Importing report id:  :id', array(':id' => $report['incident_id']));

				// Grab categoires
				$categories = DB::query(Database::SELECT, '
					SELECT c.id FROM category c
					LEFT JOIN incident_category ic ON (c.id = ic.category_id)
					WHERE incident_id = :incident_id
				')
				->parameters(array(':incident_id' => $report['incident_id']))
				->execute($this->db2);

				//@TODO grab custom fields

				$tags = array();
				foreach ($categories as $cat)
				{
					if (isset($this->tag_map[$cat['id']]))
					{
						$tags[] = $this->tag_map[$cat['id']];
					}
				}

				// Get media - dummy query since we can't save this yet.
				$media = DB::query(Database::SELECT, '
					SELECT * FROM media m
					WHERE incident_id = :incident_id
				')
				->parameters(array(':incident_id' => $report['incident_id']))
				->execute($this->db2);

				$news_media = $video_media = $photo_media = array();
				foreach($media as $m)
				{
					switch ($m['media_type'])
					{
						// photo
						case 1:
							break;
						// video
						case 2:
							$video_media[] = array(
								'value' => $m['media_link']
							);
							break;
						// news
						case 4:
							$news_media[] = array(
								'value' => $m['media_link']
							);
							break;
					}
				}

				$user_id = $author_email = $author_realname = NULL;
				// If report has user email
				if (! empty($report['email']) AND isset($this->user_map[$report['email']]))
				{
					$user_id = $this->user_map[$report['email']];
				}
				// Source report has person info
				elseif (! empty($report['person_email']) OR ! empty($report['person_first']) OR ! empty($report['person_last']))
				{
					// Already got this user
					if (isset($this->user_map[$report['person_email']]))
					{
						$user_id = $this->user_map[$report['email']];
					}
					// New user
					else
					{
						$author_realname = $report['person_first'] . ' ' . $report['person_last'];
						$author_email = $report['person_email'];
					}
				}

				$body = json_encode(array(
					"form" => $form_id,
					"title" => substr($report['incident_title'], 0, 150), // Make we don't exceed the max length.
					"content" => $report['incident_description'],
					"user_id" => $user_id,
					"author_email" => $author_email,
					"author_realname" => $author_realname,
					"type" => "report",
					"status" => $report['incident_active'] ? 'published' : 'draft',
					"locale" => "en_US",
					"values" => array(
						"original_id" => $report['incident_id'],
						"date" => $report['incident_date'],
						"location_name" => $report['location_name'],
						"location" => (is_numeric($report['latitude']) AND is_numeric($report['longitude']))  ? array(
							'lat' => $report['latitude'],
							'lon' => $report['longitude']
						) : NULL,
						"verified" => $report['incident_verified'],
						"source" => $source[$report['incident_mode']],
						"news" => $news_media,
						"photo" => $photo_media,
						"video" => $video_media,
					),
					"tags" => $tags
				));

				$this->logger->add(Log::DEBUG, "POSTing..\n :body", array(':body' => $body));
				$post_response = $this->_request("api/v2/posts")
				->method(Request::POST)
				->body($body)
				->execute();

				if ($post_response->status() != 200)
				{
					throw new Minion_Exception("Error creating post. Server returned :status. Details:\n\n :error \n\n Request Body: :body", array(
						':status' => $post_response->status(),
						':error' => $post_response->body(),
						':body' => $body
						));
				}

				$post = json_decode($post_response->body(), TRUE);
				if (! isset($post['id']))
				{
					throw new Minion_Exception("Error creating post. Details:\n\n :error \n\n Request Body: :body", array(
						':error' => $post_response->body(),
						':body' => $body
						));
				}

				// If we auto created author user
				if (isset($author['email']) AND !empty($post['user']['id']))
				{
					$users[$author['email']] = $post['user']['id'];
				}

				$this->logger->add(Log::INFO, "new post id: :id", array(':id' => $post['id']));

				// Dummy query since we can't do anything with this data yet.
				$comments = DB::query(Database::SELECT, '
					SELECT * FROM comment c
					WHERE incident_id = :incident_id
				')
				->parameters(array(':incident_id' => $report['incident_id']))
				->execute($this->db2);

				unset($post_response, $post, $body, $tags, $incident, $categories, $media, $comments, $customfields);

				$processed++;
				$post_count++;
			}

			unset($reports);

			// Set offset for next batch
			$offset += $limit;

			if ($processed == 0) $done = TRUE;
		}

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $post_count;
	}

	/**
	 * Import categories
	 *
	 * @param string $url       Base url of deployment to import from
	 * @param string $username  Username on 2.x deploymnet
	 * @param string $password  Password on 2.x deployment
	 * @return int count of categories imported
	 */
	protected function _api_import_categories($url, $username, $password)
	{
		$category_count = 0;

		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Upgrade', __FUNCTION__);
		}

		// @todo handle existing categories

		// Create categories
		$this->logger->add(Log::NOTICE, 'Fetching categories');
		// FIXME will only get visible categories. Probably just have to caveat with that
		$cat_request = $this->_request("{$url}index.php/api?task=categories")
			->headers('Authorization', 'Basic ' . base64_encode($username . ':' . $password));
		$cat_response = $cat_request->execute();
		$cat_body = json_decode($cat_response->body(), TRUE);

		if ($cat_response->status() != 200 OR ! isset($cat_body['payload']['categories']))
		{
			throw new Minion_Exception("Error getting categories. Details:\n\n HTTP Status: :status, Body: :error",
				array(
					':status' => $cat_response->status(),
					':error' => $cat_response->body()
				)
			);
		}

		$categories = $cat_body['payload']['categories'];

		unset($form_response, $form_body, $cat_request, $cat_response, $cat_body);

		$this->tag_map = array();
		$tag_titles = array();

		// First pass - just saving parents
		$this->logger->add(Log::NOTICE, 'Importing parent categories');
		foreach($categories as $obj)
		{
			$category = $obj['category'];

			// Skip child categories
			if ($category['parent_id'] != 0) continue;

			// Remove duplicate tags
			if (isset($tag_titles[$category['title']][$category['parent_id']]))
			{
				// Copy existing tag id into tag_map, then skip creating this category
				$dupe_cat = $tag_titles[$category['title']][$category['parent_id']];
				$this->tag_map[$category['id']] = $this->tag_map[$dupe_cat];
				continue;
			}
			else
			{
				// Add title to registry
				$tag_titles[$category['title']][$category['parent_id']] = $category['id'];
			}

			// FIXME nowhere to store icon or translations
			$body = json_encode(array(
				"tag" => $category['title'],
				"type" => "category",
				"description" => $category['description'],
				"color" => $category['color'],
				"priority" => $category['position'],
				"parent" => 0
			));
			$tag_response = $this->_request("api/v2/tags")
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

			// Save into tag_map so we know the parent id later
			$this->tag_map[$category['id']] = $tag['id'];

			$category_count++;

			unset($tag_response, $tag, $body, $category);
		}

		// Second pass - all non top level categories
		$this->logger->add(Log::NOTICE, 'Importing rest of categories');
		foreach($categories as $obj)
		{
			$category = $obj['category'];

			if ($category['parent_id'] == 0) continue;

			// Remove duplicate tags
			if (isset($tag_titles[$category['title']][$category['parent_id']]))
			{
				// Copy existing tag id into tag_map, then skip creating this category
				$dupe_cat = $tag_titles[$category['title']][$category['parent_id']];
				$this->tag_map[$category['id']] = $this->tag_map[$dupe_cat];
				continue;
			}
			else
			{
				// Add title to registry
				$tag_titles[$category['title']][$category['parent_id']] = $category['id'];
			}

			// FIXME nowhere to store icon or translations
			$body = json_encode(array(
				"tag" => $category['title'],
				"type" => "category",
				"description" => $category['description'],
				"color" => $category['color'],
				"priority" => $category['position'],
				"parent" => isset($this->tag_map[$category['parent_id']]) ? $this->tag_map[$category['parent_id']] : 0
			));
			$tag_response = $this->_request("api/v2/tags")
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

			// Save into tag_map so we know the new id later
			$this->tag_map[$category['id']] = $tag['id'];

			$category_count++;

			unset($tag_response, $tag, $body, $category);
		}

		unset($categories);

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $category_count;
	}

	protected function _sql_import_categories()
	{
		$category_count = 0;
		$this->tag_map = array();
		$tag_titles = array();

		// @todo handle existing categories

		$this->logger->add(Log::NOTICE, 'Fetching categories');
		$categories = DB::query(Database::SELECT, 'SELECT * FROM category ORDER BY parent_id ASC, id ASC')->execute($this->db2);

		$this->logger->add(Log::NOTICE, 'Importing categories');
		foreach ($categories as $category)
		{
			// Remove duplicate tags
			if (isset($tag_titles[$category['category_title']][$category['parent_id']]))
			{
				// Copy existing tag id into tag_map, then skip creating this category
				$dupe_cat = $tag_titles[$category['category_title']][$category['parent_id']];
				$this->tag_map[$category['id']] = $this->tag_map[$dupe_cat];
				continue;
			}
			else
			{
				// Add title to registry
				$tag_titles[$category['category_title']][$category['parent_id']] = $category['id'];
			}

			// FIXME nowhere to store icon or translations
			// FIXME nowhere to store visiblity
			$body = json_encode(array(
				"tag" => $category['category_title'],
				"type" => "category",
				"description" => $category['category_description'],
				"color" => $category['category_color'],
				"slug" => URL::title($category['category_title'].'-'.$category['id']), // Hack to handle dupe titles
				"priority" => $category['category_position'],
				"parent" => isset($this->tag_map[$category['parent_id']]) ? $this->tag_map[$category['parent_id']] : 0
			));
			$tag_response = $this->_request("api/v2/tags")
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
			$this->tag_map[$category['id']] = $tag['id'];

			$category_count++;

			unset($tag_response, $tag, $body, $category);
		}

		return $category_count;
	}

	/**
	 * Import users
	 */
	protected function _sql_import_users()
	{
		$user_count = 0;
		$this->user_map = array();

		// Grab existing users, and add to user map
		$existing_users_response = $this->_request("api/v2/users")
			->method(Request::GET)
			->execute();
		$existing_users = json_decode($existing_users_response->body(), TRUE);
		if (! isset($existing_users['count']))
		{
			throw new Minion_Exception("Error getting existing users. Details:\n\n :error", array(
				':error' => $existing_users_response->body()
				));
		}
		foreach ($existing_users['results'] as $user)
		{
			$this->user_map[$user['email']] = $user['id'];
			$this->user_map[$user['username']] = $user['id'];
		}

		$this->logger->add(Log::NOTICE, 'Fetching users');
		$users = DB::query(Database::SELECT, 'SELECT * FROM users ORDER BY id ASC')->execute($this->db2);

		$this->logger->add(Log::NOTICE, 'Importing users');
		foreach ($users as $user)
		{
			// Skip any existing users
			if (isset($this->user_map[$user['email']]) OR isset($this->user_map[$user['username']])) continue;

			$body = json_encode(array(
				"email" => Valid::email($user['email']) ? $user['email'] : NULL,
				"realname" => $user['name'],
				"username" => $user['username'],
				'password' => $this->_get_random_str(),
			));
			$user_response = $this->_request("api/v2/users")
			->method(Request::POST)
			->body($body)
			->execute();

			$user = json_decode($user_response->body(), TRUE);
			if (! isset($user['id']))
			{
				throw new Minion_Exception("Error creating user. Details:\n\n :error \n\n Request Body: :body", array(
					':error' => $user_response->body(),
					':body' => $body
					));
			}
			$this->user_map[$user['email']] = $user['id'];
			$this->user_map[$user['username']] = $user['id'];

			$user_count++;

			unset($user_response, $user, $body);
		}

		return $user_count;
	}

	/**
	 * Create 2.x style form
	 * @return form id
	 * @todo auto detect existing form and/or attributes
	 */
	protected function _create_form()
	{
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Upgrade', __FUNCTION__);
		}

		// Create 2.x style reports form
		$form_data = View::factory('minion/task/ushahidi/form_json')->render();

		$form_response = $this->_request("api/v2/forms")
			->method(Request::POST)
			->body($form_data)
			->execute();
		$form_body = json_decode($form_response->body(), TRUE);

		if ($form_response->status() != 200 OR ! isset($form_body['id']))
		{
			throw new Minion_Exception("Error creating form. Maybe the form already exists? \nYou can use --clean to wipe the DB before import\nError Details:\n HTTP Status :status, Body: :error", array(':status' => $form_response->status(), ':error' => $form_response->body()));
		}

		$this->logger->add(Log::INFO, "Created form id: :form_id", array(':form_id' => $form_body['id']));

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $form_body['id'];
	}

	/**
	 * Generates a random alpha-numeric string
	 *
	 * @param length
	 * @return string
	 */
	protected function _get_random_str($length = 24)
	{
		// Characters to be used for random string generatino
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+[]{};:,./?`~';

		// Split the pool into an array of characters
		$pool = str_split($pool, 1);

		$max = count($pool) - 1;

		$str = '';
		for ($i=0; $i < $length; $i++)
		{
			$str .= $pool[mt_rand(0, $max)];
		}

		// Ensure the string has at least one digit and one letter
		if (ctype_alpha($str))
		{
			// Add a random digit at a randomly chosen position
			$str[mt_rand(0, $length - 1)] = chr(mt_rand(23, 61));
		}
		elseif (ctype_digit($str))
		{
			// Add a random character at a randomly chosen position
			$str[mt_rand(0, $length - 1)] = chr(mt_rand(65, 90));
		}

		return $str;
	}

}
