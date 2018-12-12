<?php

namespace Tests\Unit\App\ImportUshahidiV2\Mappers;

use Ushahidi\App\ImportUshahidiV2\Mappers\ReporterUserMapper;
use Ushahidi\Core\Entity\User;
use Tests\TestCase;
use Mockery as M;
use Faker;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class ReporterUserMapperTest extends TestCase
{
    /**
     * @dataProvider reporterProvider
     */
    public function testMap($input, $expected)
    {
        $mapper = new ReporterUserMapper();

        $user = $mapper(1, $input);

        $this->assertInstanceOf(User::class, $user);
        $this->assertArraySubset(
            $expected,
            $user->asArray(),
            true,
            "User didn't match. Actual user was: ". var_export($user->asArray(), true)
        );
    }

    public function reporterProvider()
    {
        return [
            'email-reporter' => [
                'input' => [
                    'reporter_email' => 'jimmy@jimssite.test',
                    'service_account' => 'jim@jimssite.test',
                    'reporter_first' => 'Jim',
                    'reporter_last' => 'Hanks',
                    'service_name' => 'Email',
                ],
                'expected' => [
                    'email' => 'jim@jimssite.test',
                    'realname' => 'Jim Hanks',
                    'role' => null,
                    'password' => null,
                    'contacts' => [
                        [
                            'type' => 'email',
                            'data_source' => 'email',
                            'contact' => 'jim@jimssite.test',
                            'can_notify' => true
                        ],
                    ],
                ],
            ],
            'sms-reporter' => [
                'input' => [
                    'reporter_email' => 'a@abc.test',
                    'service_account' => '123456',
                    'reporter_first' => 'John',
                    'reporter_last' => 'Jack',
                    'service_name' => 'SMS',
                ],
                'expected' => [
                    'email' => 'a@abc.test',
                    'realname' => 'John Jack',
                    'role' => null,
                    'password' => null,
                    'contacts' => [
                        [
                            'type' => 'phone',
                            'data_source' => null,
                            'contact' => '123456',
                            'can_notify' => true
                        ],
                    ],
                ],
            ],
            'twitter-reporter' => [
                'input' => [
                    'reporter_email' => 'admin@ushahidi.com',
                    'service_account' => 'ushahidi',
                    'reporter_first' => 'Ushahidi',
                    'reporter_last' => '',
                    'service_name' => 'Twitter',
                ],
                'expected' => [
                    'email' => 'admin@ushahidi.com',
                    'realname' => 'Ushahidi',
                    'role' => null,
                    'password' => null,
                    'contacts' => [
                        [
                            'type' => 'twitter',
                            'data_source' => 'twitter',
                            'contact' => 'ushahidi',
                            'can_notify' => true
                        ],
                    ],
                ],
            ],
            'other-reporter' => [
                'input' => [
                    'reporter_email' => 'whatever@ushahidi.com',
                    'service_account' => 'whatever',
                    'reporter_first' => '',
                    'reporter_last' => 'Whatever',
                    'service_name' => 'Insta',
                ],
                'expected' => [
                    'email' => 'whatever@ushahidi.com',
                    'realname' => 'Whatever',
                    'role' => null,
                    'password' => null,
                    'contacts' => [
                        [
                            'type' => 'insta',
                            'data_source' => 'insta',
                            'contact' => 'whatever',
                            'can_notify' => true
                        ],
                    ],
                ],
            ],
        ];
    }
}
