<?php

/**
* Ushahidi Platform Signer Tool
*
* @author     Ushahidi Team <team@ushahidi.com>
* @package    Ushahidi\Platform
* @copyright  2014 Ushahidi
* @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
*/

namespace Ushahidi\Core\Tool;

use Log;

class Signer
{
    protected $authToken;
    public function __construct($authToken)
    {
        $this->authToken = $authToken;
    }

    public function sign($fullUrl, $json)
    {
        return $this->computeSignature($fullUrl, $json);
    }

    public function computeSignature($url, $json)
    {
        $url = $url . $json;

        // This function calculates the HMAC hash of the data with the key
        // passed in
        // Note: hash_hmac requires PHP 5 >= 5.1.2 or PECL hash:1.1-1.5
        // Or http://pear.php.net/package/Crypt_HMAC/
        return base64_encode(hash_hmac("sha256", $url, $this->authToken, true));
    }

    public function validate($expectedSignature, $url, $data = "")
    {
        return hash_equals(
            $this->computeSignature($url, $data),
            $expectedSignature
        );
    }
}
