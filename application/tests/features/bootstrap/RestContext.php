<?php
use Behat\Behat\Context\BehatContext;
use Symfony\Component\Yaml\Yaml;

/**
 * Rest context.
 */
class RestContext extends BehatContext
{

	private $_restObject        = null;
	private $_restObjectType    = null;
	private $_restObjectMethod  = 'get';
	private $_client            = null;
	private $_response          = null;
	private $_requestUrl        = null;
	private $_apiUrl           = '/api/v2';

	private $_parameters			= array();

	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 */
	public function __construct(array $parameters)
	{
		// Initialize your context here

		$this->_restObject  = new stdClass();
		$this->_client      = new Guzzle\Service\Client();
		$this->_parameters = $parameters;
	}

	public function getParameter($name)
	{
		if (count($this->_parameters) === 0) {


			throw new \Exception('Parameters not loaded!');
		} else {

			$parameters = $this->_parameters;
			return (isset($parameters[$name])) ? $parameters[$name] : null;
		}
	}

	/**
	 * @Given /^that I want to make a new "([^"]*)"$/
	 */
	public function thatIWantToMakeANew($objectType)
	{
		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'post';
	}

	/**
	 * @Given /^that I want to find a "([^"]*)"$/
	 */
	public function thatIWantToFindA($objectType)
	{
		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'get';
	}

	/**
	 * @Given /^that I want to get all "([^"]*)"$/
	 */
	public function thatIWantToGetAll($objectType)
	{
		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'get';
	}

	/**
	 * @Given /^that I want to delete a "([^"]*)"$/
	 */
	public function thatIWantToDeleteA($objectType)
	{
		$this->_restObjectType   = ucwords(strtolower($objectType));
		$this->_restObjectMethod = 'delete';
	}

	/**
	 * @Given /^that the request "([^"]*)" is:$/
	 */
	public function thatTheRequestIs($propertyName, $propertyValue)
	{
		$this->_restObject->$propertyName = $propertyValue;
	}

	/**
	 * @Given /^that its "([^"]*)" is "([^"]*)"$/
	 */
	public function thatItsIs($propertyName, $propertyValue)
	{
		$this->_restObject->$propertyName = $propertyValue;
	}
		
	/**
	 * @When /^I request "([^"]*)"$/
	 */
	public function iRequest($pageUrl)
	{
		$baseUrl 			= $this->getParameter('base_url');
		$this->_requestUrl 	= $baseUrl.$this->_apiUrl.$pageUrl;

		switch (strtoupper($this->_restObjectMethod)) {
			case 'GET':
				$request = (array)$this->_restObject;
				$id = ( isset($request['id']) ) ? $request['id'] : '';
				$response = $this->_client
					->get($this->_requestUrl.'/'.$id)
					->send();
				break;
			case 'POST':
				$postFields = (array)$this->_restObject;
				$response = $this->_client
					->post($this->_requestUrl,null,$postFields['data'])
					->send();
				break;
			case 'DELETE':
				$request = (array)$this->_restObject;
				$id = ( isset($request['id']) ) ? $request['id'] : '';
				$response = $this->_client
					->delete($this->_requestUrl.'/'.$id)
					->send();
				break;
		}
		$this->_response = $response;
	}

	/**
	 * @Then /^the response is JSON$/
	 */
	public function theResponseIsJson()
	{
		$data = json_decode($this->_response->getBody(true));

		if (empty($data)) {
			throw new Exception("Response was not JSON\n" . $this->_response);
		}
	}

	/**
	 * @Given /^the response has a "([^"]*)" property$/
	 */
	public function theResponseHasAProperty($propertyName)
	{
		$data = json_decode($this->_response->getBody(true));

		if (!empty($data)) {
			if (!isset($data->$propertyName)) {
				throw new Exception("Property '".$propertyName."' is not set!\n");
			}
		} else {
			throw new Exception("Response was not JSON\n" . $this->_response->getBody(true));
		}
	}

	/**
	 * @Then /^the "([^"]*)" property equals "([^"]*)"$/
	 */
	public function thePropertyEquals($propertyName, $propertyValue)
	{
		$data = json_decode($this->_response->getBody(true));

		if (!empty($data)) {
			if (!isset($data->$propertyName)) {
				throw new Exception("Property '".$propertyName."' is not set!\n");
			}
			if ($data->$propertyName !== $propertyValue) {
				throw new \Exception('Property value mismatch! (given: '.$propertyValue.', match: '.$data->$propertyName.')');
			}
		} else {
			throw new Exception("Response was not JSON\n" . $this->_response->getBody(true));
		}
	}

	/**
	 * @Given /^the type of the "([^"]*)" property is ([^"]*)$/
	 */
	public function theTypeOfThePropertyIsNumeric($propertyName,$typeString)
	{
		$data = json_decode($this->_response->getBody(true));

		if (!empty($data)) {
			if (!isset($data->$propertyName)) {
				throw new Exception("Property '".$propertyName."' is not set!\n");
			}
			// check our type
			switch (strtolower($typeString)) {
				case 'numeric':
					if (!is_numeric($data->$propertyName)) {
						throw new Exception("Property '".$propertyName."' is not of the correct type: ".$theTypeOfThePropertyIsNumeric."!\n");
					}
					break;
			}

		} else {
			throw new Exception("Response was not JSON\n" . $this->_response->getBody(true));
		}
	}

	/**
	 * @Then /^the response status code should be (\d+)$/
	 */
	public function theResponseStatusCodeShouldBe($httpStatus)
	{
		if ((string)$this->_response->getStatusCode() !== $httpStatus) {
			throw new \Exception('HTTP code does not match '.$httpStatus.
				' (actual: '.$this->_response->getStatusCode().')');
		}
	}

	 /**
	 * @Then /^echo last response$/
	 */
	public function echoLastResponse()
	{
		$this->printDebug(
			$this->_requestUrl."\n\n".
			$this->_response
		);
	}
}