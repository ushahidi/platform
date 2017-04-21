<?php

/**
 * Ushahidi Rest Context
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Behat\Behat\Context\Context;
use Symfony\Component\Yaml\Yaml;

/**
 * Rest context.
 */
class RestContext implements Context
{

	private $_restObject        = null;
	private $_restObjectType    = null;
	private $_restObjectMethod  = 'get';
	private $_client            = null;
	private $_response          = null;
	private $_requestUrl        = null;
	private $_apiUrl            = 'api/v3';

	private $_parameters        = array();
	private $_headers           = [
		'Accept' => 'application/json'
	];
	private $_postFields        = array();
	private $_postFiles        = array();

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 */
	public function __construct($baseUrl, $proxyUrl = FALSE)
	{
		$this->_restObject = new stdClass();

		$options = array();
		if($proxyUrl)
		{
			$options['curl.options'] = array(CURLOPT_PROXY => $proxyUrl);
		}

		$this->_client = new Guzzle\Service\Client($baseUrl, $options);
	}

	/**
	 * Automatically set bearer token so you can forget about it
	 * @BeforeScenario @oauth2Skip
	 */
	public function setDefaultBearerAuth()
	{
		$this->thatTheOauthTokenIs('defaulttoken');
	}

	/**
	 * @Given /^that I want to make a new "([^"]*)"$/
	 */
	public function thatIWantToMakeANew($objectType)
	{
		// Reset _restObject
		$this->_restObject = new stdClass();

		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'post';
	}

	/**
	 * @Given /^that I want to import a "([^"]*)"$/
	 */
	public function thatIWantToImportA($objectType)
	{
		// Reset _restObject
		$this->_restObject = new stdClass();

		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'post';
	}


	/**
	 * @Given /^that I want to submit a new "([^"]*)"$/
	 */
	public function thatIWantToSubmitANew($objectType)
	{
		// Reset _restObject
		$this->_restObject = new stdClass();

		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'post';
	}

	/**
	 * @Given /^that I want to update a "([^"]*)"$/
	 * @Given /^that I want to update an "([^"]*)"$/
	 */
	public function thatIWantToUpdateA($objectType)
	{
		// Reset _restObject
		$this->_restObject = new stdClass();

		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'put';
	}

	/**
	 * @Given /^that I want to find a "([^"]*)"$/
	 * @Given /^that I want to find an "([^"]*)"$/
	 */
	public function thatIWantToFindA($objectType)
	{
		// Reset _restObject
		$this->_restObject = new stdClass();

		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'get';
	}

	/**
	 * @Given /^that I want to get all "([^"]*)"$/
	 */
	public function thatIWantToGetAll($objectType)
	{
		// Reset _restObject
		$this->_restObject = new stdClass();

		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'get';
	}

	/**
	 * @Given /^that I want to delete a "([^"]*)"$/
	 * @Given /^that I want to delete an "([^"]*)"$/
	 */
	public function thatIWantToDeleteA($objectType)
	{
		// Reset _restObject
		$this->_restObject = new stdClass();

		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'delete';
	}

	/**
	 * @Given /^that the request "([^"]*)" is:$/
	 * @Given /^that the request "([^"]*)" is "([^"]*)"$/
	 * @Given /^that its "([^"]*)" is "([^"]*)"$/
	 */
	public function thatTheRequestPropertyIs($propertyName, $propertyValue)
	{
		$this->_restObject->$propertyName = $propertyValue;
	}

	/**
	 * @Given /^that the request "([^"]*)" header is:$/
	 * @Given /^that the request "([^"]*)" header is "([^"]*)"$/
	 */
	public function thatTheRequestHeaderIs($headerName, $headerValue)
	{
		$this->_headers[$headerName] = $headerValue;
	}

	/**
     * @Given /^that the response "([^"]*)" header is "([^"]*)"$/
     */
    public function thatTheResponseHeaderIs($headerName, $headerValue)
    {
		$this->_headers[$headerName] = $headerValue;
    }

	/**
	 * @Given /^that the post field "([^"]*)" is:$/
	 * @Given /^that the post field "([^"]*)" is "([^"]*)"$/
	 */
	public function thatThePostFieldIs($fieldName, $fieldValue)
	{
		$this->_postFields[$fieldName] = $fieldValue;
	}

	/**
	 * @Given /^that the post file "([^"]*)" is:$/
	 * @Given /^that the post file "([^"]*)" is "([^"]*)"$/
	 */
	public function thatThePostFileIs($fieldName, $fieldValue)
	{
		$this->_postFiles[$fieldName] = $fieldValue;
	}

	/**
	 * @When /^I request "([^"]*)"$/
	 */
	public function iRequest($pageUrl)
	{
		$this->_requestUrl 	= $this->_apiUrl.$pageUrl;

		switch (strtoupper($this->_restObjectMethod)) {
			case 'GET':
				$request = (array)$this->_restObject;
				$id = ( isset($request['id']) ) ? $request['id'] : '';
				$http_request = $this->_client
					->get($this->_requestUrl.'/'.$id);

				if (isset($request['query string']))
				{
					$url = $http_request->getUrl(TRUE);
					$url->setQuery((string) trim($request['query string']));
					$http_request->setUrl($url);
				}
				break;
			case 'POST':
				$request = (array)$this->_restObject;
				// If post fields or files are set assume this is a 'normal' POST request
				if ($this->_postFields OR $this->_postFiles)
				{
					$http_request = $this->_client
						->post($this->_requestUrl)
						->addPostFields($this->_postFields)
						->addPostFiles($this->_preparePostFileData($this->_postFiles));
				}
				// Otherwise assume we have JSON
				else
				{
					$http_request = $this->_client
						->post($this->_requestUrl)
						->setBody($request['data']);
				}
				break;
			case 'PUT':
				$request = (array)$this->_restObject;
				$id = ( isset($request['id']) ) ? $request['id'] : '';
				$http_request = $this->_client
					->put($this->_requestUrl.'/'.$id)
					->setBody($request['data']);
				break;
			case 'DELETE':
				$request = (array)$this->_restObject;
				$id = ( isset($request['id']) ) ? $request['id'] : '';
				$http_request = $this->_client
					->delete($this->_requestUrl.'/'.$id);
				break;
		}

		try {
			$http_request
				->addHeaders($this->_headers)
				->send();
		} catch (Guzzle\Http\Exception\BadResponseException $e) {
			// Don't care.
			// 4xx and 5xx statuses are valid error responses
		}

		// Get response object
		$this->_response = $http_request->getResponse();

		// Create fake response object if Guzzle doesn't give us one
		if (! $this->_response instanceof Guzzle\Http\Message\Response)
		{
			$this->_response = new Guzzle\Http\Message\Response(null, null, null);
		}
	}

	/**
	 * @Then /^the response is JSON$/
	 */
	public function theResponseIsJson()
	{
		$data = json_decode($this->_response->getBody(TRUE), TRUE);

		// Check for NULL not empty - since [] and {} will be empty but valid
		if ($data === NULL) {

			// Get further error info
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					$error = 'No errors';
				break;
				case JSON_ERROR_DEPTH:
					$error = 'Maximum stack depth exceeded';
				break;
				case JSON_ERROR_STATE_MISMATCH:
					$error = 'Underflow or the modes mismatch';
				break;
				case JSON_ERROR_CTRL_CHAR:
					$error = 'Unexpected control character found';
				break;
				case JSON_ERROR_SYNTAX:
					$error = 'Syntax error, malformed JSON';
				break;
				case JSON_ERROR_UTF8:
					$error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
				break;
				default:
					$error = 'Unknown error';
				break;
			}

			throw new Exception("Response was not JSON\nBody:" . $this->_response->getBody(TRUE) . "\nError: " . $error );
		}
	}

	/**
	 * @Then /^the response is JSONP$/
	 */
	public function theResponseIsJsonp()
	{
		$result = preg_match('/^.+\(({.+})\)$/', $this->_response->getBody(TRUE), $matches);

		if ($result != 1 OR empty($matches[1]))
		{
			throw new Exception("Response was not JSONP\nBody:" . $this->_response->getBody(TRUE));
		}

		$data = json_decode($matches[1]);

		// Check for NULL not empty - since [] and {} will be empty but valid
		if ($data === NULL) {
			// Get further error info
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					$error = 'No errors';
				break;
				case JSON_ERROR_DEPTH:
					$error = 'Maximum stack depth exceeded';
				break;
				case JSON_ERROR_STATE_MISMATCH:
					$error = 'Underflow or the modes mismatch';
				break;
				case JSON_ERROR_CTRL_CHAR:
					$error = 'Unexpected control character found';
				break;
				case JSON_ERROR_SYNTAX:
					$error = 'Syntax error, malformed JSON';
				break;
				case JSON_ERROR_UTF8:
					$error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
				break;
				default:
					$error = 'Unknown error';
				break;
			}

			throw new Exception("Response was not JSONP\nBody:" . $this->_response->getBody(TRUE) . "\nError: " . $error );
		}
	}

	/**
	 * @Given /^the response has a "([^"]*)" property$/
	 * @Given /^the response has an "([^"]*)" property$/
	 */
	public function theResponseHasAProperty($propertyName)
	{
		$data = json_decode($this->_response->getBody(TRUE), TRUE);

		$this->theResponseIsJson();

		if (Arr::path($data, $propertyName) === NULL) {
			throw new Exception("Property '".$propertyName."' is not set!\n");
		}
	}

	/**
	 * @Given /^the response does not have a "([^"]*)" property$/
	 * @Given /^the response does not have an "([^"]*)" property$/
	 */
	public function theResponseDoesNotHaveAProperty($propertyName)
	{
		$data = json_decode($this->_response->getBody(TRUE), TRUE);

		$this->theResponseIsJson();

		if (Arr::path($data, $propertyName) !== NULL) {
			throw new Exception("Property '".$propertyName."' is set but should not be!\n");
		}
	}

	/**
	 * @Then /^the "([^"]*)" property equals "([^"]*)"$/
	 */
	public function thePropertyEquals($propertyName, $propertyValue)
	{
		$data = json_decode($this->_response->getBody(TRUE), TRUE);

		$this->theResponseIsJson();

		$actualPropertyValue = Arr::path($data, $propertyName);

		if ($actualPropertyValue === NULL) {
			throw new Exception("Property '".$propertyName."' is not set!\n");
		}
		// Check the value - note this has to use != since $propertValue is always a string so strict comparison would fail.
		if ($actualPropertyValue != $propertyValue) {
			throw new \Exception('Property value mismatch on \''.$propertyName.'\'! (given: '.$propertyValue.', match: '.$actualPropertyValue.')');
		}
	}

	/**
	 * @Then /^the "([^"]*)" property is true$/
	 */
	public function thePropertyIsTrue($propertyName)
	{
		$data = json_decode($this->_response->getBody(TRUE), TRUE);

		$this->theResponseIsJson();

		$actualPropertyValue = Arr::path($data, $propertyName);

		if ($actualPropertyValue === NULL) {
			throw new Exception("Property '".$propertyName."' is not set!\n");
		}
		if ($actualPropertyValue !== TRUE) {
			throw new \Exception('Property \''.$propertyName.'\' is not true! (match: '.$actualPropertyValue.')');
		}
	}

	/**
	 * @Then /^the "([^"]*)" property is false$/
	 */
	public function thePropertyIsFalse($propertyName)
	{
		$data = json_decode($this->_response->getBody(TRUE), TRUE);

		$this->theResponseIsJson();

		$actualPropertyValue = Arr::path($data, $propertyName);

		if ($actualPropertyValue === NULL) {
			throw new Exception("Property '".$propertyName."' is not set!\n");
		}
		if ($actualPropertyValue !== FALSE) {
			throw new \Exception('Property \''.$propertyName.'\' is not false! (match: '.$actualPropertyValue.')');
		}
	}

	/**
	 * @Given /^the "([^"]*)" property contains "([^"]*)"$/
	 */
	public function thePropertyContains($propertyName, $propertyContainsValue)
	{

		$data = json_decode($this->_response->getBody(TRUE), TRUE);

		$this->theResponseIsJson();

		$actualPropertyValue = Arr::path($data, $propertyName);

		if ($actualPropertyValue === NULL) {
			throw new Exception("Property '".$propertyName."' is not set!\n");
		}

		if (is_array($actualPropertyValue) AND ! in_array($propertyContainsValue, $actualPropertyValue)) {
			throw new \Exception('Property \''.$propertyName.'\' does not contain value! (given: '.$propertyContainsValue.', match: '.json_encode($actualPropertyValue).')');
		}
		elseif (is_string($actualPropertyValue) AND strpos($actualPropertyValue, $propertyContainsValue) === FALSE)
		{
			throw new \Exception('Property \''.$propertyName.'\' does not contain value! (given: '.$propertyContainsValue.', match: '.$actualPropertyValue.')');
		}
		elseif (!is_array($actualPropertyValue) AND !is_string($actualPropertyValue))
		{
			throw new \Exception("Property '".$propertyName."' could not be compared. Must be string or array.\n");
		}
	}

	/**
	 * @Given /^the "([^"]*)" property count is "([^"]*)"$/
	 */
	public function thePropertyCountIs($propertyName, $propertyCountValue)
	{

		$data = json_decode($this->_response->getBody(TRUE), TRUE);

		$this->theResponseIsJson();

		$actualPropertyValue = Arr::path($data, $propertyName);

		if ($actualPropertyValue === NULL) {
			throw new Exception("Property '".$propertyName."' is not set!\n");
		}

		if (is_array($actualPropertyValue) AND count($actualPropertyValue) != $propertyCountValue) {
			throw new \Exception('Property \''.$propertyName.'\' count does not match! (given: '.$propertyCountValue.', match: '.count($actualPropertyValue).')');
		}
		elseif (!is_array($actualPropertyValue))
		{
			throw new \Exception("Property '".$propertyName."' could not be compared. Must be an array.\n");
		}
	}

	/**
	 * @Given /^the type of the "([^"]*)" property is "([^"]*)"$/
	 */
	public function theTypeOfThePropertyIs($propertyName, $typeString)
	{
		$data = json_decode($this->_response->getBody(TRUE), TRUE);

		$this->theResponseIsJson();

		$actualPropertyValue = Arr::path($data, $propertyName);

		if ($actualPropertyValue === NULL) {
			throw new Exception("Property '".$propertyName."' is not set!\n");
		}
		// check our type
		switch (strtolower($typeString)) {
			case 'numeric':
				if (!is_numeric($actualPropertyValue)) {
					throw new Exception("Property '".$propertyName."' is not of the correct type: ".$typeString."!\n");
				}
				break;
		}
	}

	/**
	 * @Then /^the "([^"]*)" property is empty$/
	 */
	public function thePropertyIsEmpty($propertyName)
	{
		$data = json_decode($this->_response->getBody(TRUE), TRUE);

		$this->theResponseIsJson();

		$actualPropertyValue = Arr::path($data, $propertyName);

		if (!empty($actualPropertyValue)) {
			throw new Exception("Property '{$propertyName}' is not empty!\n");
		}
	}

	/**
	 * @Then /^the guzzle status code should be (\d+)$/
	 */
	public function theRestResponseStatusCodeShouldBe($httpStatus)
	{
		if ((string)$this->_response->getStatusCode() !== $httpStatus) {
			throw new \Exception('HTTP code does not match '.$httpStatus.
				' (actual: '.$this->_response->getStatusCode().')');
		}
	}

	/**
	 * @Then /^the "([^"]*)" header should exist$/
	 */
	public function theRestHeaderShouldExist($header)
	{
		if (!$this->_response->hasHeader($header)) {
			throw new \Exception('HTTP header does not exist '.$header );
		}
	}

	/**
	 * @Then /^the the ([^"]*)" header should be "([^"]*)"$/
	 */
	public function theRestHeaderShouldExistBe($header, $contents)
	{
		if ((string)$this->_response->getHeader($header) !== $contents) {
			throw new \Exception('HTTP header ' . $header . ' does not match '.$contents.
				' (actual: '.$this->_response->getHeader($header).')');
		}
	}


	 /**
	 * @Then /^echo last response$/
	 */
	public function echoLastResponse()
	{
		var_dump(
			$this->_requestUrl."\n\n".
			$this->_response
		);
	}

	/**
	 * @Given /^that the api_url is "([^"]*)"$/
	 */
	public function thatTheApiUrlIs($api_url)
	{
		$this->_apiUrl = $api_url;
	}

	/**
	 * @AfterScenario
	 */
	public function afterScenarioCheckError(Behat\Behat\Hook\Scope\AfterScenarioScope $scope)
	{
		// If scenario failed, dump response
		if (!$scope->getTestResult()->isPassed() AND $this->_response)
		{
			$this->echoLastResponse();
		}
	}

	private function _preparePostFileData($postFiles)
	{
		//Check if post files is not empty
		if ( count($postFiles) > 0)
		{
			array_walk_recursive($postFiles, array($this, '_prefix_app_path'));
			return $postFiles;
		}
		return $postFiles;
	}

	/**
	 * Make the path to upload files to, relative to the application directory
	 *
	 * @param  string $item the path to the file to be uploaded
	 * @return string       path to application folder
	 */
	private function _prefix_app_path(&$item)
	{
		$item = DOCROOT.'/../'.$item;
	}

	/**
	 * @Given /^that I want to count all "([^"]*)"$/
	 */
	public function thatIWantToCountAll($objectType)
	{
		// Reset _restObject
		$this->_restObject = new stdClass();

		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'get';
	}

	// Map tokens to users
	// Needs ot match data in Base.yml
	private $tokenUserMap = [
		'testanon' => null,
		'testingtoken' => 2,
		'defaulttoken' => 2,
		'testadminuser' => 2,
		'testbasicuser' => 1,
		'testbasicuser2' => 3,
		'testmanager' => 6,
		'testimporter' => 7
	];

	/**
	 * @Given /^that the oauth token is "([^"]*)"$/
	 */
	public function thatTheOauthTokenIs($tokenId)
	{
		$key = new \League\OAuth2\Server\CryptKey("file://".\Laravel\Passport\Passport::keyPath('oauth-private.key'));
		$scope = new \Laravel\Passport\Bridge\Scope('*');
		$client = new \Laravel\Passport\Bridge\Client('demoapp', 'demoapp', '/');

		$accessToken = new \Laravel\Passport\Bridge\AccessToken($this->tokenUserMap[$tokenId], [$scope]);
		$accessToken->setIdentifier($tokenId);
		$accessToken->setExpiryDateTime((new \DateTime())->add(new \DateInterval('PT1H')));
		$accessToken->setClient($client);
		$token = $accessToken->convertToJwt($key);

		$this->_headers['Authorization'] = "Bearer $token";
	}

}
