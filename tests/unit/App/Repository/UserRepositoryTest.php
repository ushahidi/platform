<?php

namespace Tests\Unit\App\Repository;

use Ushahidi\App\Repository\UserRepository;
use Ushahidi\Core\Entity\User;
use Tests\TestCase;
use Tests\DatabaseTransactions;
use Mockery as M;
use Faker;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class UserRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetResetToken()
    {
        $db = M::mock(\Ohanzee\Database::class);
        $resolver = M::mock(\Ushahidi\App\Multisite\OhanzeeResolver::class);
        $resolver->shouldReceive('connection')->andReturn($db);

        $repo = new UserRepository($resolver);
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

    public function testCreateMany()
    {
        $faker = Faker\Factory::create();

        // Generate user data
        $user1 = new User([
            'email' => $faker->email,
            'realname' => $faker->name,
            'role' => 'user'
        ]);
        $user2 = new User([
            'email' => $faker->email,
            'realname' => $faker->name,
            'role' => 'user',
            'contacts' => [
                ['contact' => 'ushahidi', 'type' => 'twitter'],
            ]
        ]);
        $user3 = new User([
            'email' => $faker->email,
            'realname' => $faker->name,
            'password' => $faker->password,
            'role' => 'user',
            'contacts' => [
                ['contact' => 'ushbot', 'type' => 'twitter', 'can_notify' => 0],
                ['contact' => '12345678', 'type' => 'phone', 'can_notify' => 1],
            ]
        ]);

        $repo = service('repository.user');
        $inserted = $repo->createMany(collect([
            $user1,
            $user2,
            $user3,
        ]));

        $this->assertCount(3, $inserted);
        $this->seeInOhanzeeDatabase('users', [
            'id' => $inserted[0],
            'email' => $user1->email,
            'realname' => $user1->realname
        ]);
        $this->seeInOhanzeeDatabase('users', [
            'id' => $inserted[1],
            'email' => $user2->email,
            'realname' => $user2->realname
        ]);
        $this->seeInOhanzeeDatabase('users', [
            'id' => $inserted[2],
            'email' => $user3->email,
            'realname' => $user3->realname,
        ]);

        // Ensure unhashed password isn't saved
        $this->notSeeInOhanzeeDatabase('users', [
            'email' => $user3->email,
            'password' => $user3->password,
        ]);

        $this->seeInOhanzeeDatabase('contacts', [
            'user_id' => $inserted[1],
            'contact' => 'ushahidi',
            'can_notify' => 0,
            'type' => 'twitter',
        ]);
        $this->seeInOhanzeeDatabase('contacts', [
            'user_id' => $inserted[2],
            'contact' => '12345678',
            'can_notify' => 1,
            'type' => 'phone',
        ]);
        $this->seeInOhanzeeDatabase('contacts', [
            'user_id' => $inserted[2],
            'contact' => 'ushbot',
            'can_notify' => 0,
            'type' => 'twitter',
        ]);
    }
}
