<?php

namespace Ushahidi\Tests\Unit\Modules\V2\Mappers;

use Ushahidi\Modules\V2\Mappers\UserMapper;
use Ushahidi\Core\Entity\User;
use Ushahidi\Tests\Unit\Modules\V2\ImportMock;
use Ushahidi\Tests\TestCase;
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
        $import = ImportMock::forId(1);
        $result = $mapper($import, $input);

        $this->assertIsArray($result);
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
        $import = ImportMock::forId(1);
        $result = $mapper($import, $input);

        $this->assertIsArray($result);
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
        $import = ImportMock::forId(1);
        $result = $mapper($import, $input);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result);
        $user = $result['result'];

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('user', $user->role);
        $this->assertEquals($input['email'], $user->email);
        $this->assertEquals($input['name'], $user->realname);
    }
}
