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
use Ushahidi\Core\Entity\ApiKeyRepository;
use Log;

class Verifier
{
    protected $apiKeyRepo;

    public function __construct($apiKeyRepo)
    {
        $this->apiKeyRepo = $apiKeyRepo;
    }

    public function checkApiKey($api_key)
    {
        if ($api_key) {
            // Get api key and compare
            return $this->apiKeyRepo->apiKeyExists($api_key);
        }

        return false;
    }

    public function checkSignature($signature, $shared_secret, $url, $data)
    {
        if ($signature) {
            // Validate signature
            $signer = new Signer($shared_secret);
            return $signer->validate($signature, $url, $data);
        }
        return false;
    }

    public function verified($signature, $api_key, $shared_secret, $url, $data)
    {
        return $this->checkApiKey($api_key) && $this->checkSignature($signature, $shared_secret, $url, $data);
    }
}
