<?php

namespace Tests\Integration\Bootstrap;

/**
 * Ushahidi Rest Context
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Yaml\Yaml;
use stdClass;

/**
 * Rest context.
 */
class RestContext implements Context
{

	private $restObject        = null;
	private $restObjectType    = null;
	private $restObjectMethod  = 'get';
	private $client            = null;
	private $response          = null;
	private $requestUrl        = null;
	private $apiUrl            = 'api/v3';

	private $parameters        = array();
	private $headers           = array();
	private $postFields        = array();
	private $postFiles        = array();

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 */
	public function __construct($baseUrl, $proxyUrl = false)
	{
		$this->restObject = new stdClass();

		$options = array();
		if ($proxyUrl) {
			$options['curl.options'] = array(CURLOPT_PROXY => $proxyUrl);
		}

		$this->client = new \Guzzle\Service\Client($baseUrl, $options);
	}

	/**
	 * Automatically set bearer token so you can forget about it
	 * @BeforeScenario @oauth2Skip
	 */
	public function setDefaultBearerAuth()
	{
		$this->thatTheRequestHeaderIs('Authorization', 'Bearer defaulttoken');
	}

	/**
	 * @Given /^that I want to make a new "([^"]*)"$/
	 */
	public function thatIWantToMakeANew($objectType)
	{
		// Reset restObject
		$this->restObject = new stdClass();

		$this->restObjectType   = ucwords(strtolower($objectType));
		$this->restObjectMethod = 'post';
	}

	/**
	 * @Given /^that I want to import a "([^"]*)"$/
	 */
	public function thatIWantToImportA($objectType)
	{
		// Reset restObject
		$this->restObject = new stdClass();

		$this->restObjectType   = ucwords(strtolower($objectType));
		$this->restObjectMethod = 'post';
	}


	/**
	 * @Given /^that I want to submit a new "([^"]*)"$/
	 */
	public function thatIWantToSubmitANew($objectType)
	{
		// Reset restObject
		$this->restObject = new stdClass();

		$this->restObjectType   = ucwords(strtolower($objectType));
		$this->restObjectMethod = 'post';
	}

	/**
	 * @Given /^that I want to update a "([^"]*)"$/
	 * @Given /^that I want to update an "([^"]*)"$/
	 */
	public function thatIWantToUpdateA($objectType)
	{
		// Reset restObject
		$this->restObject = new stdClass();

		$this->restObjectType   = ucwords(strtolower($objectType));
		$this->restObjectMethod = 'put';
	}

	/**
	 * @Given /^that I want to find a "([^"]*)"$/
	 * @Given /^that I want to find an "([^"]*)"$/
	 */
	public function thatIWantToFindA($objectType)
	{
		// Reset restObject
		$this->restObject = new stdClass();

		$this->restObjectType   = ucwords(strtolower($objectType));
		$this->restObjectMethod = 'get';
	}

	/**
	 * @Given /^that I want to get all "([^"]*)"$/
	 */
	public function thatIWantToGetAll($objectType)
	{
		// Reset restObject
		$this->restObject = new stdClass();

		$this->restObjectType   = ucwords(strtolower($objectType));
		$this->restObjectMethod = 'get';
	}

	/**
	 * @Given /^that I want to delete a "([^"]*)"$/
	 * @Given /^that I want to delete an "([^"]*)"$/
	 */
	public function thatIWantToDeleteA($objectType)
	{
		// Reset restObject
		$this->restObject = new stdClass();

		$this->restObjectType   = ucwords(strtolower($objectType));
		$this->restObjectMethod = 'delete';
	}

	/**
	 * @Given /^that the request "([^"]*)" is:$/
	 * @Given /^that the request "([^"]*)" is "([^"]*)"$/
	 * @Given /^that its "([^"]*)" is "([^"]*)"$/
	 */
	public function thatTheRequestPropertyIs($propertyName, $propertyValue)
	{
		$this->restObject->$propertyName = $propertyValue;
	}

	/**
	 * @Given /^that the request "([^"]*)" header is:$/
	 * @Given /^that the request "([^"]*)" header is "([^"]*)"$/
	 */
	public function thatTheRequestHeaderIs($headerName, $headerValue)
	{
		$this->headers[$headerName] = $headerValue;
	}

	/**
     * @Given /^that the response "([^"]*)" header is "([^"]*)"$/
     */
    public function thatTheResponseHeaderIs($headerName, $headerValue)
    {
		$this->headers[$headerName] = $headerValue;
    }

	/**
	 * @Given /^that the post field "([^"]*)" is:$/
	 * @Given /^that the post field "([^"]*)" is "([^"]*)"$/
	 */
	public function thatThePostFieldIs($fieldName, $fieldValue)
	{
		$this->postFields[$fieldName] = $fieldValue;
	}

	/**
	 * @Given /^that the post file "([^"]*)" is:$/
	 * @Given /^that the post file "([^"]*)" is "([^"]*)"$/
	 */
	public function thatThePostFileIs($fieldName, $fieldValue)
	{
		$this->postFiles[$fieldName] = $fieldValue;
	}

	/**
	 * @When /^I request "([^"]*)"$/
	 */
	public function iRequest($pageUrl)
	{
		$this->requestUrl 	= $this->apiUrl.$pageUrl;

		switch (strtoupper($this->restObjectMethod)) {
			case 'GET':
				$request = (array)$this->restObject;
				$id = ( isset($request['id']) ) ? $request['id'] : '';
				$http_request = $this->client
					->get($this->requestUrl.'/'.$id);

				if (isset($request['query string'])) {
					$url = $http_request->getUrl(true);
					$url->setQuery((string) trim($request['query string']));
					$http_request->setUrl($url);
				}
				break;
			case 'POST':
				$request = (array)$this->restObject;
				// If post fields or files are set assume this is a 'normal' POST request
				if ($this->postFields or $this->postFiles) {
					$http_request = $this->client
						->post($this->requestUrl)
						->addPostFields($this->postFields)
						->addPostFiles($this->preparePostFileData($this->postFiles));
				} else {
					// Otherwise assume we have JSON
					$http_request = $this->client
						->post($this->requestUrl)
						->setBody($request['data']);
				}
				break;
			case 'PUT':
				$request = (array)$this->restObject;
				$id = ( isset($request['id']) ) ? $request['id'] : '';
				$http_request = $this->client
					->put($this->requestUrl.'/'.$id)
					->setBody($request['data']);
				break;
			case 'DELETE':
				$request = (array)$this->restObject;
				$id = ( isset($request['id']) ) ? $request['id'] : '';
				$http_request = $this->client
					->delete($this->requestUrl.'/'.$id);
				break;
		}

		try {
			$http_request
				->addHeaders($this->headers)
				->send();
		} catch (\Guzzle\Http\Exception\BadResponseException $e) {
			// Don't care.
			// 4xx and 5xx statuses are valid error responses
		}

		// Get response object
		$this->response = $http_request->getResponse();

		// Create fake response object if Guzzle doesn't give us one
		if (! $this->response instanceof \Guzzle\Http\Message\Response) {
			$this->response = new \Guzzle\Http\Message\Response(null, null, null);
		}
	}

	/**
	 * @Then the csv response body should have heading:
	 */
	public function theCsvResponseBodyShouldHaveHeading(PyStringNode $string)
	{
		$data = $this->response->getBody(true);
		$data = explode("\n", $data);
		if (!$data[0] || $data[0] !== $string->getRaw()) {
			throw new \Exception("Response {{$data[0]}} \n did not match \n{{$string->getRaw()}}");
		}
	}

	/**
	 * @Then the csv response body should have :arg1 columns in row :arg2
	 */
	public function theCsvResponseBodyShouldHaveColumnsInRow($arg1, $arg2)
	{
		$data = $this->response->getBody(true);
		$rows = explode("\n", $data);
		$columnCount = count(explode(",", $rows[$arg2]));
		if ($columnCount !== intval($arg1)) {
			throw new \Exception("Row $arg2 should have $arg1 columns. Found $columnCount");
		}
	}

	/**
	 * @Then the csv response body should equal:
	 */
	public function theCsvResponseBodyShouldEqual(PyStringNode $string)
	{
		$data = $this->response->getBody(true);
		if (trim($data) !== trim($string)) {
			throw new \Exception("Body $data is not equal to \n $string");
		}
	}


	/**
	 * @Then /^the response is JSON$/
	 */
	public function theResponseIsJson()
	{
		$data = json_decode($this->response->getBody(true), true);

		// Check for NULL not empty - since [] and {} will be empty but valid
		if ($data === null) {
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

			throw new \Exception(
				"Response was not JSON\nBody:" . $this->response->getBody(true) . "\nError: " . $error
			);
		}
	}

	/**
	 * @Then /^the response is JSONP$/
	 */
	public function theResponseIsJsonp()
	{
		$result = preg_match('/^.+\(({.+})\)$/', $this->response->getBody(true), $matches);

		if ($result != 1 or empty($matches[1])) {
			throw new \Exception("Response was not JSONP\nBody:" . $this->response->getBody(true));
		}

		$data = json_decode($matches[1]);

		// Check for NULL not empty - since [] and {} will be empty but valid
		if ($data === null) {
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

			throw new \Exception(
				"Response was not JSONP\nBody:" . $this->response->getBody(true) . "\nError: " . $error
			);
		}
	}

	/**
	 * @Given /^the response has a "([^"]*)" property$/
	 * @Given /^the response has an "([^"]*)" property$/
	 */
	public function theResponseHasAProperty($propertyName)
	{
		$data = json_decode($this->response->getBody(true), true);
		$this->theResponseIsJson();

		if (\Arr::path($data, $propertyName) === null) {
			throw new \Exception("Property '".$propertyName."' is not set!\n");
		}
	}

	/**
	 * @Given /^the response does not have a "([^"]*)" property$/
	 * @Given /^the response does not have an "([^"]*)" property$/
	 */
	public function theResponseDoesNotHaveAProperty($propertyName)
	{
		$data = json_decode($this->response->getBody(true), true);

		$this->theResponseIsJson();

		if (\Arr::path($data, $propertyName) !== null) {
			throw new \Exception("Property '".$propertyName."' is set but should not be!\n");
		}
	}

	/**
	 * @Then /^the "([^"]*)" property equals "([^"]*)"$/
	 */
	public function thePropertyEquals($propertyName, $propertyValue)
	{
		$data = json_decode($this->response->getBody(true), true);
		$this->theResponseIsJson();

		$actualPropertyValue = \Arr::path($data, $propertyName);

		if ($actualPropertyValue === null) {
			throw new \Exception("Property '".$propertyName."' is not set!\n");
		}
		// Check the value - note this has to use != since $propertValue
		// is always a string so strict comparison would fail.
		if ($actualPropertyValue != $propertyValue) {
			throw new \Exception(
				"Property value mismatch on '" . $propertyName . "'! ".
				"(given: " . $propertyValue . ", match: " . $actualPropertyValue . ")"
			);
		}
	}

	/**
	 * @Then /^the "([^"]*)" property is true$/
	 */
	public function thePropertyIsTrue($propertyName)
	{
		$data = json_decode($this->response->getBody(true), true);

		$this->theResponseIsJson();

		$actualPropertyValue = \Arr::path($data, $propertyName);

		if ($actualPropertyValue === null) {
			throw new \Exception("Property '".$propertyName."' is not set!\n");
		}
		if ($actualPropertyValue !== true) {
			throw new \Exception('Property \''.$propertyName.'\' is not true! (match: '.$actualPropertyValue.')');
		}
	}

	/**
	 * @Then /^the "([^"]*)" property is false$/
	 */
	public function thePropertyIsFalse($propertyName)
	{
		$data = json_decode($this->response->getBody(true), true);

		$this->theResponseIsJson();

		$actualPropertyValue = \Arr::path($data, $propertyName);

		if ($actualPropertyValue === null) {
			throw new \Exception("Property '".$propertyName."' is not set!\n");
		}
		if ($actualPropertyValue !== false) {
			throw new \Exception('Property \''.$propertyName.'\' is not false! (match: '.$actualPropertyValue.')');
		}
	}

	/**
	 * @Given /^the "([^"]*)" property contains "([^"]*)"$/
	 */
	public function thePropertyContains($propertyName, $propertyContainsValue)
	{

		$data = json_decode($this->response->getBody(true), true);

		$this->theResponseIsJson();

		$actualPropertyValue = \Arr::path($data, $propertyName);

		if ($actualPropertyValue === null) {
			throw new Exception("Property '".$propertyName."' is not set!\n");
		}

		if (is_array($actualPropertyValue) and ! in_array($propertyContainsValue, $actualPropertyValue)) {
			throw new \Exception(
				'Property \''.$propertyName.'\' does not contain value!' .
				'(given: '.$propertyContainsValue.', match: '.json_encode($actualPropertyValue).')'
			);
		} elseif (is_string($actualPropertyValue) and strpos($actualPropertyValue, $propertyContainsValue) === false) {
			throw new \Exception(
				'Property \''.$propertyName.'\' does not contain value!' .
				'(given: '.$propertyContainsValue.', match: '.$actualPropertyValue.')'
			);
		} elseif (!is_array($actualPropertyValue) and !is_string($actualPropertyValue)) {
			throw new \Exception(
				"Property '".$propertyName."' could not be compared. Must be string or array.\n"
			);
		}
	}

	/**
	 * @Given /^the "([^"]*)" property does not contain "([^"]*)"$/
	 */
	public function thePropertyDoesNotContains($propertyName, $propertyContainsValue)
	{

		$data = json_decode($this->response->getBody(true), true);

		$this->theResponseIsJson();

		$actualPropertyValue = \Arr::path($data, $propertyName);

		if ($actualPropertyValue === null) {
			throw new Exception("Property '".$propertyName."' is not set!\n");
		}

		if (is_array($actualPropertyValue) and in_array($propertyContainsValue, $actualPropertyValue)) {
			throw new \Exception(
				'Property \''.$propertyName.'\' contains value!' .
				'(given: '.$propertyContainsValue.', match: '.json_encode($actualPropertyValue).')'
			);
		} elseif (is_string($actualPropertyValue) and strpos($actualPropertyValue, $propertyContainsValue) !== false) {
			throw new \Exception(
				'Property \''.$propertyName.'\' does not contain value!' .
				'(given: '.$propertyContainsValue.', match: '.$actualPropertyValue.')'
			);
		} elseif (!is_array($actualPropertyValue) and !is_string($actualPropertyValue)) {
			throw new \Exception(
				"Property '".$propertyName."' could not be compared. Must be string or array.\n"
			);
		}
	}

	/**
	 * @Given /^the "([^"]*)" property count is "([^"]*)"$/
	 */
	public function thePropertyCountIs($propertyName, $propertyCountValue)
	{

		$data = json_decode($this->response->getBody(true), true);

		$this->theResponseIsJson();

		$actualPropertyValue = \Arr::path($data, $propertyName);

		if ($actualPropertyValue === null) {
			throw new \Exception("Property '".$propertyName."' is not set!\n");
		}

		if (is_array($actualPropertyValue) and count($actualPropertyValue) != $propertyCountValue) {
			throw new \Exception(
				'Property \''.$propertyName.'\' count does not match!' .
				'(given: '.$propertyCountValue.', match: '.count($actualPropertyValue).')'
			);
		} elseif (!is_array($actualPropertyValue)) {
			throw new \Exception("Property '".$propertyName."' could not be compared. Must be an array.\n");
		}
	}

	/**
	 * @Given /^the type of the "([^"]*)" property is "([^"]*)"$/
	 */
	public function theTypeOfThePropertyIs($propertyName, $typeString)
	{
		$data = json_decode($this->response->getBody(true), true);

		$this->theResponseIsJson();

		$actualPropertyValue = \Arr::path($data, $propertyName);

		if ($actualPropertyValue === null) {
			throw new \Exception("Property '".$propertyName."' is not set!\n");
		}
		// check our type
		switch (strtolower($typeString)) {
			case 'numeric':
				if (!is_numeric($actualPropertyValue)) {
					throw new \Exception("Property '".$propertyName."' is not of the correct type: ".$typeString."!\n");
				}
				break;
			case 'int':
				if (!is_int($actualPropertyValue)) {
					throw new \Exception("Property '".$propertyName."' is not of the correct type: ".$typeString."!\n");
				}
				break;
		}
	}

	/**
	 * @Then /^the "([^"]*)" property is empty$/
	 */
	public function thePropertyIsEmpty($propertyName)
	{
		$data = json_decode($this->response->getBody(true), true);

		$this->theResponseIsJson();

		$actualPropertyValue = \Arr::path($data, $propertyName);

		if (!empty($actualPropertyValue)) {
			throw new \Exception("Property '{$propertyName}' is not empty!\n");
		}
	}

	/**
	 * @Then /^the guzzle status code should be (\d+)$/
	 */
	public function theRestResponseStatusCodeShouldBe($httpStatus)
	{
		if ((string)$this->response->getStatusCode() !== $httpStatus) {
			throw new \Exception('HTTP code does not match '.$httpStatus.
				' (actual: '.$this->response->getStatusCode().')');
		}
	}

	/**
	 * @Then /^the "([^"]*)" header should exist$/
	 */
	public function theRestHeaderShouldExist($header)
	{
		if (!$this->response->hasHeader($header)) {
			throw new \Exception('HTTP header does not exist '.$header);
		}
	}

	/**
	 * @Then /^the the ([^"]*)" header should be "([^"]*)"$/
	 */
	public function theRestHeaderShouldExistBe($header, $contents)
	{
		if ((string)$this->response->getHeader($header) !== $contents) {
			throw new \Exception('HTTP header ' . $header . ' does not match '.$contents.
				' (actual: '.$this->response->getHeader($header).')');
		}
	}


	 /**
	 * @Then /^echo last response$/
	 */
	public function echoLastResponse()
	{
		var_dump(
			$this->requestUrl."\n\n".
			$this->response
		);
	}

	/**
	 * @Given /^that the api_url is "([^"]*)"$/
	 */
	public function thatTheApiUrlIs($api_url)
	{
		$this->apiUrl = $api_url;
	}

	/**
	 * @AfterScenario
	 */
	public function afterScenarioCheckError(\Behat\Behat\Hook\Scope\AfterScenarioScope $scope)
	{
		// If scenario failed, dump response
		if (!$scope->getTestResult()->isPassed() and $this->response) {
			$this->echoLastResponse();
		}
	}

	private function preparePostFileData($postFiles)
	{
		//Check if post files is not empty
		if (count($postFiles) > 0) {
			array_walk_recursive($postFiles, array($this, 'prefixAppPath'));
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
	private function prefixAppPath(&$item)
	{
		$item = DOCROOT.$item;
	}

	/**
	 * @Given /^that I want to count all "([^"]*)"$/
	 */
	public function thatIWantToCountAll($objectType)
	{
		// Reset restObject
		$this->restObject = new stdClass();

		$this->restObjectType   = ucwords(strtolower($objectType));
		$this->restObjectMethod = 'get';
	}
}
