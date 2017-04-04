<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Signer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Signer;

class Ushahidi_Signer_Signature implements Signer
{
  protected $authToken;

  public function __construct($authToken)
  {
    $this->authToken = $authToken;
  }

  public function sign($fullUrl, $data)
  {
    return computeSignature($fullUrl, $data);
  }

  public function computeSignature($url, $json) {
    $url = $url . $json;
    // This function calculates the HMAC hash of the data with the key
    // passed in
    // Note: hash_hmac requires PHP 5 >= 5.1.2 or PECL hash:1.1-1.5
    // Or http://pear.php.net/package/Crypt_HMAC/
    return base64_encode(hash_hmac("sha256", $url, $this->authToken, true));
  }

  public function validate($expectedSignature, $url, $json) {
    return self::compare(
      $this->computeSignature($url, $json),
      $expectedSignature
    );
  }
  /**
  * Time insensitive compare, function's runtime is governed by the length
  * of the first argument, not the difference between the arguments.
  * @param $a string First part of the comparison pair
  * @param $b string Second part of the comparison pair
  * @return bool True if $a == $b, false otherwise.
  */
  public static function compare($a, $b)
  {
    $result = true;
    if (strlen($a) != strlen($b)) {
      return false;
    }
    if (!$a && !$b) {
      return true;
    }
    $limit = strlen($a);
    for ($i = 0; $i < $limit; ++$i) {
      if ($a[$i] != $b[$i]) {
        $result = false;
      }
    }
    return $result;
  }
}
