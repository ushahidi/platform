<?php

namespace Tests\Unit\App\ImportUshahidiV2\Mappers;

use Ushahidi\App\ImportUshahidiV2\Mappers\UserMapper;
use Ushahidi\Core\Entity\User;
use Tests\TestCase;
use Mockery as M;
use Faker;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class UserMapperTest extends TestCase
{
    public function testMapAdmin()
    {
        $faker = Faker\Factory::create();
        $input = [
            'email' => $faker->email,
            'name' => $faker->name,
            'role' => 'admin,member,login'
        ];

        $mapper = new UserMapper();
        $result = $mapper(1, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $user = $result['result'];

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('admin', $user->role);
        $this->assertEquals($input['email'], $user->email);
        $this->assertEquals($input['name'], $user->realname);
    }

    public function testMapSuperAdmin()
    {
        $faker = Faker\Factory::create();
        $input = [
            'email' => $faker->email,
            'name' => $faker->name,
            'role' => 'superadmin,member'
        ];

        $mapper = new UserMapper();
        $result = $mapper(1, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $user = $result['result'];

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('admin', $user->role);
        $this->assertEquals($input['email'], $user->email);
        $this->assertEquals($input['name'], $user->realname);
    }

    public function testMapOtherRole()
    {
        $faker = Faker\Factory::create();

        $input = [
            'email' => $faker->email,
            'name' => $faker->name,
            'role' => 'member,login,something'
        ];

        $mapper = new UserMapper();
        $result = $mapper(1, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $user = $result['result'];

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('user', $user->role);
        $this->assertEquals($input['email'], $user->email);
        $this->assertEquals($input['name'], $user->realname);
    }
}
