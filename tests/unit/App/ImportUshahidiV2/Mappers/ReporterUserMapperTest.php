<?php

namespace Tests\Unit\App\ImportUshahidiV2\Mappers;

use Ushahidi\App\ImportUshahidiV2\Mappers\ReporterUserMapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use Ushahidi\Core\Entity\Contact;
use Tests\Unit\App\ImportUshahidiV2\ImportMock;
use Tests\TestCase;
use Mockery as M;
use Faker;

use Illuminate\Support\Collection;

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
        $mappingRepo = M::mock(ImportMappingRepository::class);
        $mappingRepo->shouldReceive('getAllMappingIDs')
            ->with(1, 'user')
            ->andReturn(collect([ ($input['user_id']) => ($expected['user_id']) ]));

        $mapper = new ReporterUserMapper($mappingRepo);
        $import = ImportMock::forId(1);
        $result = $mapper($import, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $contact = $result['result'];

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertArraySubset(
            $expected,
            $contact->asArray(),
            true,
            "User didn't match. Actual user was: ". var_export($contact->asArray(), true)
        );
    }

    public function reporterProvider()
    {
        return [
            'email-reporter' => [
                'input' => [
                    'user_id' => '1',
                    'reporter_email' => 'jimmy@jimssite.test',
                    'service_account' => 'jim@jimssite.test',
                    'reporter_first' => 'Jim',
                    'reporter_last' => 'Hanks',
                    'service_name' => 'Email',
                ],
                'expected' => [
                    'user_id' => 2,
                    'contact' => 'jim@jimssite.test',
                    'data_source' => 'email',
                    'type' => 'email',
                    'can_notify' => true
                ],
            ],
            'sms-reporter' => [
                'input' => [
                    'user_id' => '3',
                    'reporter_email' => 'a@abc.test',
                    'service_account' => '123456',
                    'reporter_first' => 'John',
                    'reporter_last' => 'Jack',
                    'service_name' => 'SMS',
                ],
                'expected' => [
                    'user_id' => 4,
                    'contact' => '123456',
                    'type' => 'phone',
                    'can_notify' => true
                ],
            ],
            'twitter-reporter' => [
                'input' => [
                    'user_id' => '5',
                    'reporter_email' => 'admin@ushahidi.com',
                    'service_account' => 'ushahidi',
                    'reporter_first' => 'Ushahidi',
                    'reporter_last' => '',
                    'service_name' => 'Twitter',
                ],
                'expected' => [
                    'user_id' => 6,
                    'type' => 'twitter',
                    'data_source' => 'twitter',
                    'contact' => 'ushahidi',
                    'can_notify' => true
                ],
            ],
            'other-reporter' => [
                'input' => [
                    'user_id' => '7',
                    'reporter_email' => 'whatever@ushahidi.com',
                    'service_account' => 'whatever',
                    'reporter_first' => '',
                    'reporter_last' => 'Whatever',
                    'service_name' => 'Insta',
                ],
                'expected' => [
                    'user_id' => 8,
                    'type' => 'insta',
                    'data_source' => 'insta',
                    'contact' => 'whatever',
                    'can_notify' => true
                ],
            ],
        ];
    }
}
