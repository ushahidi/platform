<?php

/**
 * Unit tests for Signature Verifier
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\Unit\Core\Tool;

use Ushahidi\Core\Tool\Verifier;
use Ushahidi\Core\Entity\ApiKeyRepository;
use Tests\TestCase;
use Mockery as M;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class VerifierTest extends TestCase
{
    protected $apiKey = "eebc4a87-9267-4491-989c-690fd8f10466";
    protected $sharedSecret = "iamasharedsecret";

    protected function makeSig($sharedSecret, $url, $payload)
    {
        $data = $url . $payload;

        return base64_encode(hash_hmac("sha256", $data, $sharedSecret, true));
    }

    public function testValidSignature()
    {
        $repo = M::mock(ApiKeyRepository::class);
        $verifier = new Verifier($repo);
        $url = "http://localhost:8000/api/v3/exports/external/count/1?api_key={$this->apiKey}";
        $payload = ['data' => 'things'];
        $signature = $this->makeSig($this->sharedSecret, $url, json_encode($payload));

        $repo->shouldReceive('apiKeyExists')->with($this->apiKey)->andReturn(true);

        $return = $verifier->verified($signature, $this->apiKey, $this->sharedSecret, $url, json_encode($payload));

        $this->assertTrue($return);
    }

    public function testValidSignatureWithEmptyPayload()
    {
        $repo = M::mock(ApiKeyRepository::class);
        $verifier = new Verifier($repo);
        $url = "http://localhost:8000/api/v3/exports/external/count/1?api_key={$this->apiKey}";
        $payload = "";
        $signature = $this->makeSig($this->sharedSecret, $url, "");

        $repo->shouldReceive('apiKeyExists')->with($this->apiKey)->andReturn(true);

        $return = $verifier->verified($signature, $this->apiKey, $this->sharedSecret, $url, "");

        $this->assertTrue($return);
    }

    public function testInvalidApiKeySignature()
    {
        $repo = M::mock(ApiKeyRepository::class);
        $verifier = new Verifier($repo);
        $url = "http://localhost:8000/api/v3/exports/external/count/1?api_key={$this->apiKey}";
        $payload = ['data' => 'things'];
        $signature = $this->makeSig($this->sharedSecret, $url, json_encode($payload));

        $repo->shouldReceive('apiKeyExists')->with($this->apiKey)->andReturn(false);

        $return = $verifier->verified($signature, $this->apiKey, $this->sharedSecret, $url, json_encode($payload));

        $this->assertFalse($return);
    }

    public function testInvalidSecretSignature()
    {
        $repo = M::mock(ApiKeyRepository::class);
        $verifier = new Verifier($repo);
        $url = "http://localhost:8000/api/v3/exports/external/count/1?api_key={$this->apiKey}";
        $payload = ['data' => 'things'];
        $signature = $this->makeSig('notthesecret', $url, json_encode($payload));

        $repo->shouldReceive('apiKeyExists')->with($this->apiKey)->andReturn(true);

        $return = $verifier->verified($signature, $this->apiKey, $this->sharedSecret, $url, json_encode($payload));

        $this->assertFalse($return);
    }

    public function testInvalidDataSignature()
    {
        $repo = M::mock(ApiKeyRepository::class);
        $verifier = new Verifier($repo);
        $url = "http://localhost:8000/api/v3/exports/external/count/1?api_key={$this->apiKey}";
        $payload = ['data' => 'things'];
        $signature = $this->makeSig($this->sharedSecret, $url, json_encode(['tampered' => 'data']));

        $repo->shouldReceive('apiKeyExists')->with($this->apiKey)->andReturn(true);

        $return = $verifier->verified($signature, $this->apiKey, $this->sharedSecret, $url, json_encode($payload));

        $this->assertFalse($return);
    }

    public function testCheckSignature()
    {
        $repo = M::mock(ApiKeyRepository::class);
        $verifier = new Verifier($repo);
        $url = "http://localhost:8000/api/v3/exports/external/count/1?api_key={$this->apiKey}";
        $payload = ['data' => 'things'];
        $signature = $this->makeSig($this->sharedSecret, $url, json_encode($payload));

        $return = $verifier->checkSignature($signature, $this->sharedSecret, $url, json_encode($payload));

        $this->assertTrue($return);
    }

    public function testCheckApiKey()
    {
        $repo = M::mock(ApiKeyRepository::class);
        $verifier = new Verifier($repo);

        $repo->shouldReceive('apiKeyExists')->with($this->apiKey)->andReturn(true);

        $return = $verifier->checkApiKey($this->apiKey);

        $this->assertTrue($return);
    }
}
