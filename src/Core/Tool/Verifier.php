<?php

/**
* Ushahidi Platform Verifier Tool
*
* @author     Ushahidi Team <team@ushahidi.com>
* @package    Ushahidi\Platform
* @copyright  2014 Ushahidi
* @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
*/

namespace Ushahidi\Core\Tool;

use Ushahidi\Core\Tool\Signer;

class Verifier
{
    protected $signature;
    protected $api_key;
    protected $shared_secret;
    protected $fullURL;
    protected $data;

    public function __construct($signature, $api_key, $shared_secret, $fullURL, $data)
    {
        $this->signature = $signature;
        $this->api_key = $api_key;
        $this->shared_secret = $shared_secret;
        $this->fullURL = $fullURL;
        $this->data = $data;
    }

    public function checkApiKey()
	{

		if ($this->api_key) {
			// Get api key and compare
			return service('repository.apikey')->apiKeyExists($this->api_key);
		}

		return false;
	}

	public function checkSignature()
	{
		if ($this->signature) {
			//Validate signature
			$signer = new Signer($this->shared_secret);
			return $signer->validate($this->signature, $this->fullURL, $this->data);
		}
		return false;
    }
    
    public function verified()
    {
        return $this->checkApiKey() && $this->checkSignature();
    }
}
