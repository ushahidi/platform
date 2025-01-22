<?php

namespace Ushahidi\Tests\Integration\Bootstrap;

/**
 * Ushahidi Rest Context
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use stdClass;
use Aura\Di\Exception;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * Rest context.
 */
class RestContext implements Context
{
    private $restObject = null;

    private $restObjectType = null;

    private $restObjectMethod = 'get';

    private $client = null;

    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    private $response = null;

    private $requestUrl = null;

    private $apiUrl = 'api/v3';

    private $parameters = [];

    private $headers = [
        'Accept' => 'application/json',
    ];

    private $postFields = [];

    private $postFiles = [];

    private $isBulk = false;

    const DEBUG_MODE_SWITCH_FILE_PATH = __DIR__.'/../../../bootstrap/install_debug_mode.enabled';

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     */
    public function __construct($baseUrl, $proxyUrl = false)
    {
        $this->restObject = new stdClass();

        $options = [
            'http_errors' => false,
        ];
        if ($proxyUrl) {
            $options['proxy'] = ['http'  => $proxyUrl];
        }

        $options['base_uri'] = $baseUrl;

        $this->client = new \GuzzleHttp\Client($options);
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
     * @Given /^that cache control level is set to "([^"]*)"$/
     */
    public function thatCacheControlLevelIs($levelStr)
    {
        /*
         * FIXME: somehow this is not working effectively. The middleware
         *        still gets the old values, even when calling config()
         *        *after* this function runs.
         */
        // Set config flag
        Config::set('routes.cache_control.level', strtolower($levelStr));
    }

    /**
     * @AfterScenario
     * @Given ^that cache control level is set to "([^"]*)"$/
     */
    public function disableCacheControlAfterScenario()
    {
        // Reset config flag to the "off" default
        Config::set('routes.cache_control.level', 'off');
    }

    /**
     * @Given /^that I want to make a new "([^"]*)"$/
     */
    public function thatIWantToMakeANew($objectType)
    {
        // Reset restObject
        $this->restObject = new stdClass();

        $this->restObjectType = ucwords(strtolower($objectType));
        $this->restObjectMethod = 'post';
    }

    /**
     * @Given /^that I want to import a "([^"]*)"$/
     */
    public function thatIWantToImportA($objectType)
    {
        // Reset restObject
        $this->restObject = new stdClass();

        $this->restObjectType = ucwords(strtolower($objectType));
        $this->restObjectMethod = 'post';
    }

    /**
     * @Given /^that I want to patch a "([^"]*)"$/
     * @Given /^that I want to patch an "([^"]*)"$/
     */
    public function thatIWantToPatchA($objectType)
    {
        // Reset restObject
        $this->restObject = new stdClass();
        $this->restObjectType = ucwords(strtolower($objectType));
        $this->restObjectMethod = 'patch';
    }

    /**
     * @Given /^that I want to bulk operate on "([^"]*)"$/
     */
    public function thatIWantToBulkOperate($objectType)
    {
        // Reset restObject
        $this->restObject = new stdClass();
        $this->restObjectType = ucwords(strtolower($objectType));
        $this->restObjectMethod = 'post';
        $this->isBulk = true;
    }

    /**
     * @Given /^that I want to submit a new "([^"]*)"$/
     */
    public function thatIWantToSubmitANew($objectType)
    {
        // Reset restObject
        $this->restObject = new stdClass();

        $this->restObjectType = ucwords(strtolower($objectType));
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

        $this->restObjectType = ucwords(strtolower($objectType));
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

        $this->restObjectType = ucwords(strtolower($objectType));
        $this->restObjectMethod = 'get';
    }

    /**
     * @Given /^that I want to get all "([^"]*)"$/
     */
    public function thatIWantToGetAll($objectType)
    {
        // Reset restObject
        $this->restObject = new stdClass();

        $this->restObjectType = ucwords(strtolower($objectType));
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

        $this->restObjectType = ucwords(strtolower($objectType));
        $this->restObjectMethod = 'delete';
    }

    /**
     * @Given /^that I want to make an OPTIONS request$/
     */
    public function thatIWantToMakeAnOptionsRequest()
    {
        // Reset _restObject
        $this->restObject = new stdClass();
        $this->restObjectMethod = 'options';
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
     * @Given /^that I have enabled debug mode$/
     */
    public function thatIHaveEnabledDebugMode()
    {
        fopen(self::DEBUG_MODE_SWITCH_FILE_PATH, 'w');
    }

    /**
     * @AfterScenario
     * @Given /^that I have disabled debug mode$/
     */
    public function disableDebugModeAfterScenario()
    {
        if (file_exists(self::DEBUG_MODE_SWITCH_FILE_PATH)) {
            unlink(self::DEBUG_MODE_SWITCH_FILE_PATH);
        }
    }

    /**
     * @When /^I request "([^"]*)"$/
     */
    public function iRequest($pageUrl)
    {
        $this->requestUrl = $this->apiUrl.$pageUrl;
        switch (strtoupper($this->restObjectMethod)) {
            case 'GET':
                $request = (array) $this->restObject;
                $idAttachment = (isset($request['id'])) ? '/'.$request['id'] : '';
                $response = $this->client
                    ->get($this->requestUrl.$idAttachment, [
                        'query' => isset($request['query string']) ? trim($request['query string']) : null,
                        'headers' => $this->headers,
                    ]);
                break;
            case 'POST':
                $request = (array) $this->restObject;
                $this->requestUrl = $this->isBulk ? $this->requestUrl.'/bulk' : $this->requestUrl;
                // If post fields or files are set assume this is a 'normal' POST request
                if ($this->postFiles) {
                    $response = $this->client
                        ->post($this->requestUrl, [
                            'headers' => $this->headers,
                            'multipart' => array_merge(
                                array_map(function ($v, $k) {
                                        return ['name' => $k, 'contents' => $v];
                                }, $this->postFields, array_keys($this->postFields)),
                                $this->preparePostFileData($this->postFiles)
                            ),

                        ]);
                } elseif ($this->postFields) {
                    $response = $this->client
                        ->post($this->requestUrl, [
                            'headers' => $this->headers,
                            'form_params' => $this->postFields,
                        ]);
                } else {
                    // Otherwise assume we have JSON
                    $response = $this->client
                        ->post($this->requestUrl, [
                            'headers' => $this->headers + ['Content-Type' => 'application/json'],
                            'body' => $request['data'],
                        ]);
                }
                break;
            case 'PUT':
                $request = (array) $this->restObject;
                $id = (isset($request['id'])) ? $request['id'] : '';
                $response = $this->client
                    ->put($this->requestUrl.'/'.$id, [
                        'headers' =>  $this->headers + ['Content-Type' => 'application/json'],
                        'body' => $request['data'],
                    ]);
                break;
            case 'PATCH':
                $request = (array) $this->restObject;
                $id = (isset($request['id']) && ! $this->isBulk) ? $request['id'] : '';

                $response = $this->client
                    ->patch($this->requestUrl.'/'.$id, [
                        'headers' =>  $this->headers + ['Content-Type' => 'application/json'],
                        'body' => $request['data'],
                    ]);
                break;
            case 'DELETE':
                $request = (array) $this->restObject;
                $id = (isset($request['id'])) ? $request['id'] : '';
                $response = $this->client
                    ->delete($this->requestUrl.'/'.$id, [
                        'headers' => $this->headers,
                    ]);
                break;
            case 'OPTIONS':
                $request = (array) $this->restObject;
                $id = (isset($request['id'])) ? $request['id'] : '';
                $response = $this->client
                    ->options($this->requestUrl.'/'.$id, [
                        'headers' => $this->headers,
                    ]);
                break;
        }

        // Get response object
        $this->response = $response;
    }

    /**
     * @Then the csv response body should have heading:
     */
    public function theCsvResponseBodyShouldHaveHeading(PyStringNode $string)
    {
        $data = $this->response->getBody(true);
        $data = explode("\n", $data);
        if (! $data[0] || $data[0] !== $string->getRaw()) {
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
        $columnCount = count(explode(',', $rows[$arg2]));
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

        // The response should have appropriate headers
        $content_type = $this->response->getHeaderLine('Content-Type') ?? '';
        $content_length = $this->response->getHeaderLine('Content-Length');
        if (! $content_type) {
            throw new \Exception('HTTP header Content-Type missing');
        }
        if (stripos($content_type, 'application/json') === false) {
            throw new \Exception('HTTP header Content-Type is not "application/json", instead: '.$content_type);
        }
        if (! $content_length) {
            throw new \Exception('HTTP header Content-Length is missing');
        }
        if (intval($content_length) != mb_strlen($this->response->getBody(), '8bit')) {
            throw new \Exception('HTTP header Content-Length doesn\'t match content size');
        }

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
                "Response was not JSON\nBody:".$this->response->getBody(true)."\nError: ".$error
            );
        }
    }

    /**
     * @Then /^the response is JSONP$/
     */
    public function theResponseIsJsonp()
    {
        $result = preg_match('/.+\(({.+})\);?/s', $this->response->getBody(true), $matches);

        if ($result != 1 or empty($matches[1])) {
            throw new \Exception("Response was not JSONP\nBody:".$this->response->getBody(true));
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
                "Response was not JSONP\nBody:".$this->response->getBody(true)."\nError: ".$error
            );
        }
    }

    /**
     * @Then /^the response is empty/
     */
    public function theResponseIsEmpty()
    {
        \PHPUnit\Framework\Assert::assertEquals(0, $this->response->getBody()->getSize());
    }

    /**
     * @Given /^the response has a "([^"]*)" property$/
     * @Given /^the response has an "([^"]*)" property$/
     */
    public function theResponseHasAProperty($propertyName)
    {
        $data = json_decode($this->response->getBody(true), true);
        $this->theResponseIsJson();

        if (Arr::get($data, $propertyName) === null) {
            throw new \Exception("Property '".$propertyName."' is not set!\n");
        }
    }

    /**
     * @Given /^the JSON response contains "([^"]*)"/
     */
    public function theJsonResponseContains($propertyPathPattern)
    {
        //$propertyPathPattern uses dot notation and asterisks to denote arrays

        $data = json_decode($this->response->getBody(true), true);
        $paths = explode('*', $propertyPathPattern);

        foreach ($paths as $path) {
//            *.items.*.fields
            if (Arr::get($data, $path) === null) {
                throw new \Exception("Property $path in '".$propertyPathPattern."' is not set\n");
            }
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

        if (Arr::get($data, $propertyName) !== null) {
            throw new \Exception("Property '".$propertyName."' is set but should not be!\n");
        }
    }

    /**
     * @Then /^the "([^"]*)" property is null$/
     */
    public function thePropertyIsNull($propertyName)
    {
        $data = json_decode($this->response->getBody(true), true);
        $actualPropertyValue = array_has($data, $propertyName);

        if ($actualPropertyValue !== null) {
            throw new \Exception(
                "Property $propertyName should was expected to be null."
            );
        }
    }

    /**
     * @Then /^the "([^"]*)" property equals "([^"]*)"$/
     */
    public function thePropertyEquals($propertyName, $propertyValue)
    {
        $data = json_decode($this->response->getBody(true), true);
        $this->theResponseIsJson();

        $actualPropertyValue = Arr::get($data, $propertyName);

        if ($actualPropertyValue === null) {
            throw new \Exception("Property '".$propertyName."' is not set!\n");
        }
        // Check the value - note this has to use != since $propertValue
        // is always a string so strict comparison would fail.
        if ($actualPropertyValue != $propertyValue) {
            throw new \Exception(
                "Property value mismatch on '".$propertyName."'! ".
                '(given: '.$propertyValue.', match: '.$actualPropertyValue.')'
            );
        }
    }

    /**
     * @Then /^the "([^"]*)" property is greater than or equal to "([^"]*)"$/
     */
    public function thePropertyIsGreaterThanOrEqualTo($propertyName, $propertyValue)
    {
        $data = json_decode($this->response->getBody(true), true);

        $this->theResponseIsJson();

        $actualPropertyValue = Arr::get($data, $propertyName);

        if ($actualPropertyValue === null) {
            throw new \Exception("Property '".$propertyName."' is not set!\n");
        }

        if (! $actualPropertyValue >= $propertyValue) {
            throw new \Exception(
                "Property '".$propertyName."'! ".
                'is not greater than or equal to: '.$propertyValue.', (match: '.$actualPropertyValue.')'
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

        $actualPropertyValue = Arr::get($data, $propertyName);

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

        $actualPropertyValue = Arr::get($data, $propertyName);

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

        $actualPropertyValue = Arr::get($data, $propertyName);

        if ($actualPropertyValue === null) {
            throw new Exception("Property '".$propertyName."' is not set!\n");
        }

        if (is_array($actualPropertyValue) and ! in_array($propertyContainsValue, $actualPropertyValue)) {
            throw new \Exception(
                'Property \''.$propertyName.'\' does not contain value!'.
                '(given: '.$propertyContainsValue.', match: '.json_encode($actualPropertyValue).')'
            );
        } elseif (is_string($actualPropertyValue) and strpos($actualPropertyValue, $propertyContainsValue) === false) {
            throw new \Exception(
                'Property \''.$propertyName.'\' does not contain value!'.
                '(given: '.$propertyContainsValue.', match: '.$actualPropertyValue.')'
            );
        } elseif (! is_array($actualPropertyValue) and ! is_string($actualPropertyValue)) {
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

        $actualPropertyValue = Arr::get($data, $propertyName);

        if ($actualPropertyValue === null) {
            throw new Exception("Property '".$propertyName."' is not set!\n");
        }

        if (is_array($actualPropertyValue) and in_array($propertyContainsValue, $actualPropertyValue)) {
            throw new \Exception(
                'Property \''.$propertyName.'\' contains value!'.
                '(given: '.$propertyContainsValue.', match: '.json_encode($actualPropertyValue).')'
            );
        } elseif (is_string($actualPropertyValue) and strpos($actualPropertyValue, $propertyContainsValue) !== false) {
            throw new \Exception(
                'Property \''.$propertyName.'\' does not contain value!'.
                '(given: '.$propertyContainsValue.', match: '.$actualPropertyValue.')'
            );
        } elseif (! is_array($actualPropertyValue) and ! is_string($actualPropertyValue)) {
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

        $actualPropertyValue = Arr::get($data, $propertyName);

        if ($actualPropertyValue === null) {
            throw new \Exception("Property '".$propertyName."' is not set!\n");
        }

        if (is_array($actualPropertyValue) and count($actualPropertyValue) != $propertyCountValue) {
            throw new \Exception(
                'Property \''.$propertyName.'\' count does not match!'.
                '(given: '.$propertyCountValue.', match: '.count($actualPropertyValue).')'
            );
        } elseif (! is_array($actualPropertyValue)) {
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

        $actualPropertyValue = Arr::get($data, $propertyName);

        if ($actualPropertyValue === null) {
            throw new \Exception("Property '".$propertyName."' is not set!\n");
        }
        // check our type
        switch (strtolower($typeString)) {
            case 'numeric':
                if (! is_numeric($actualPropertyValue)) {
                    throw new \Exception(
                        "Property '".$propertyName."' is not of the correct type: ".$typeString."!\n"
                    );
                }
                break;
            case 'int':
                if (! is_int($actualPropertyValue)) {
                    throw new \Exception(
                        "Property '".$propertyName."' is not of the correct type: ".$typeString."!\n"
                    );
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

        $actualPropertyValue = Arr::get($data, $propertyName);

        if (! empty($actualPropertyValue)) {
            throw new \Exception("Property '{$propertyName}' is not empty but '{$actualPropertyValue}'\n");
        }
    }

    /**
     * @Then /^the guzzle status code should be (\d+)$/
     */
    public function theRestResponseStatusCodeShouldBe($httpStatus)
    {
        if ((string) $this->response->getStatusCode() !== $httpStatus) {
            $data = json_decode($this->response->getBody(true), true);
            var_dump($data);

            throw new \Exception('HTTP code does not match '.$httpStatus.
                ' (actual: '.$this->response->getStatusCode().')');
        }
    }

    /**
     * @Then /^the "([^"]*)" header should exist$/
     */
    public function theRestHeaderShouldExist($header)
    {
        if (! $this->response->hasHeader($header)) {
            throw new \Exception('HTTP header does not exist '.$header);
        }
    }

    /**
     * @Then /^the "([^"]*)" header should be "([^"]*)"$/
     */
    public function theRestHeaderShouldBe($header, $contents)
    {
        if ($this->response->getHeaderLine(strtolower($header)) !== $contents) {
            throw new \Exception('HTTP header '.$header.' does not match '.$contents.
                ' (actual: '.$this->response->getHeaderLine(strtolower($header)).')');
        }
    }

    /**
     * @Then /^the "([^"]*)" header should contain "([^"]*)"$/
     */
    public function theRestHeaderShouldContain($header, $contents)
    {
        $header_val = $this->response->getHeaderLine(strtolower($header));
        if (! stripos($header_val, $contents)) {
            throw new \Exception('HTTP header '.$header.' does not contain '.$contents.
                ' (actual: '.$header_val.')');
        }
    }

    /**
     * @Then /^echo last response$/
     */
    public function echoLastResponse()
    {
        echo "
{$this->requestUrl}
HTTP/{$this->response->getProtocolVersion()} {$this->response->getStatusCode()} {$this->response->getReasonPhrase()}
{$this->response->getBody()}
";
        ob_flush();
    }

    /**
     * @Given /^that the api_url is "([^"]*)"$/
     */
    public function thatTheApiUrlIs($api_url)
    {
        $this->apiUrl = $api_url;
    }

    /**
     * @Given /^that the operation is in bulk$/
     */
    public function thatTheOperationIsInBulk()
    {
        $this->isBulk = true;
    }

    /**
     * @AfterScenario
     */
    public function afterScenarioCheckError(\Behat\Behat\Hook\Scope\AfterScenarioScope $scope)
    {
        // If scenario failed, dump response
        if (! $scope->getTestResult()->isPassed() and $this->response) {
            $this->echoLastResponse();
        }
    }

    private function preparePostFileData($postFiles)
    {
        //Check if post files is not empty
        if (count($postFiles) > 0) {
            return array_map(function ($v, $k) {
                return [
                    'name' => $k,
                    'contents' => fopen(base_path($v), 'r'),
                ];
            }, $postFiles, array_keys($postFiles));
        }

        return $postFiles;
    }

    /**
     * @Given /^that I want to count all "([^"]*)"$/
     */
    public function thatIWantToCountAll($objectType)
    {
        // Reset restObject
        $this->restObject = new stdClass();

        $this->restObjectType = ucwords(strtolower($objectType));
        $this->restObjectMethod = 'get';
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
        'testimporter' => 7,
        'missingtoken' => 99,
        'testnoedit' => 8,
        'testsets' => 9,
        'testsettingsmanager' => 10,
    ];

    /**
     * @Given /^that the oauth token is "([^"]*)"$/
     */
    public function thatTheOauthTokenIs($tokenId)
    {
        $keyPath = 'file://' . \Laravel\Passport\Passport::keyPath('oauth-private.key');
        $key = new \League\OAuth2\Server\CryptKey($keyPath);
        $scope = new \Laravel\Passport\Bridge\Scope('*');
        $client = new \Laravel\Passport\Bridge\Client('demoapp', 'demoapp', '/');

        $accessToken = new \Laravel\Passport\Bridge\AccessToken($this->tokenUserMap[$tokenId], [$scope], $client);
        $accessToken->setPrivateKey($key);
        $accessToken->setIdentifier($tokenId);
        $accessToken->setExpiryDateTime((new \DateTimeImmutable())->add(new \DateInterval('P1D')));
        $token = (string) $accessToken;

        $this->headers['Authorization'] = "Bearer $token";
    }
}
