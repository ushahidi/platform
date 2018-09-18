<?php

namespace Tests\Unit\App\Listener;

use Ushahidi\App\Repository\UserRepository;
use Ushahidi\Core\Entity\User;
use Tests\TestCase;
use Mockery as M;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class UserRepositoryTest extends TestCase
{

    public function testGetResetToken()
    {
        $db = M::mock(\Ohanzee\Database::class);
        $repo = new UserRepository($db);
        $user = new User(['id' => 1]);

        $db->shouldReceive('quote_table')->with('user_reset_tokens')->andReturn('`user_reset_tokens`');
        $db->shouldReceive('quote_column')->with('reset_token')->andReturn('`reset_token`');
        $db->shouldReceive('quote_column')->with('user_id')->andReturn('`user_id`');
        $db->shouldReceive('quote_column')->with('created')->andReturn('`created`');
        $db->shouldReceive('quote')
            ->with(M::any())
            ->andReturnUsing(function ($data) {
                if (is_string($data)) {
                    return "\"$data\"";
                }
                return $data;
            });
        $db->shouldReceive('query')
        ->with(
            \Ohanzee\Database::INSERT,
            \Hamcrest\Matchers::matchesPattern(
                '/INSERT INTO `user_reset_tokens` \(`reset_token`, `user_id`, `created`\) VALUES \("(.*?)", 1, (.*?)\)/'
            ),
            false,
            []
        );

        $token = $repo->getResetToken($user);

        $this->assertInternalType('string', $token);
    }
}
