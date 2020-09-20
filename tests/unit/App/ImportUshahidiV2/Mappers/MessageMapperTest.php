<?php

namespace Tests\Unit\App\ImportUshahidiV2\Mappers;

use Ushahidi\App\ImportUshahidiV2\Mappers\MessageMapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\ContactRepository;
use Tests\Unit\App\ImportUshahidiV2\ImportMock;
use Tests\TestCase;
use Mockery as M;
use Faker;

use Illuminate\Support\Collection;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class MessageMapperTest extends TestCase
{
    /**
     * @dataProvider reporterProvider
     */
    public function testMap($input, $mockCalls, $expected, $expectedDate)
    {
        $importId = 1;
        $import = ImportMock::forId($importId);

        $mappingRepo = M::mock(ImportMappingRepository::class);
        $mappingRepo->shouldReceive('getAllMappingIDs')
            ->with($importId, 'reporter')
            ->andReturn(collect($mockCalls['reporter']));
        $mappingRepo->shouldReceive('getAllMappingIDs')
            ->with($importId, 'message')
            ->andReturn(collect($mockCalls['parent']));
        $mappingRepo->shouldReceive('getAllMappingIDs')
            ->with($importId, 'user')
            ->andReturn(collect($mockCalls['user']));
        $mappingRepo->shouldReceive('getAllMappingIDs')
            ->with($importId, 'incident')
            ->andReturn(collect($mockCalls['incident']));

        $contactRepo = M::mock(ContactRepository::class);
        $contactRepo->shouldReceive('getByContact')
            ->with($mockCalls['contact'][0], $mockCalls['contact'][1])
            ->andReturn(new Contact(['id' => $mockCalls['contact'][2]]));

        $mapper = new MessageMapper($mappingRepo, $contactRepo);
        $result = $mapper($import, $input);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('result', $result);
        $message = $result['result'];

        $this->assertInstanceOf(Message::class, $message);
        $this->assertArraySubset(
            $expected,
            $message->asArray(),
            true,
            "Message didn't match. Actual data was: ". var_export($message->asArray(), true)
        );
        $this->assertEquals($expectedDate, $message->datetime);
    }

    public function reporterProvider()
    {
        return [
            'email-message' => [
                'input' => [
                    'parent_id' => null,
                    'service_account' => 'jim@jimssite.test',
                    'service_name' => 'Email',
                    'incident_id' => '5',
                    'user_id' => '7',
                    'reporter_id' => '9',
                    'service_messageid' => 'absdflkajsdf',
                    'message_detail' => 'A message',
                    'message' => 'Some subject',
                    'message_date' => '2018-10-30',
                    'message_type' => '1',
                    'latitude' => null,
                    'longitude' => null
                ],
                'mockCalls' => [
                    'incident' => ['5' => 55],
                    'user' => ['7' => 77],
                    'reporter' => ['9' => 99],
                    'contact' =>  ['jim@jimssite.test', 'email', 99],
                    'parent' => [],
                ],
                'expected' => [
                    'parent_id' => null,
                    'contact_id' => 99,
                    'post_id' => 55,
                    'user_id' => 77,
                    'data_source' => 'email',
                    'data_source_message_id' => 'absdflkajsdf',
                    'title' => 'Some subject',
                    'message' => 'A message',
                    'type' => 'email',
                    'status' => 'received',
                    'direction' => 'incoming',
                    'additional_data' => null,
                ],
                'expectedDate' => new \DateTime('2018-10-30'),
            ],
            'sms-message' => [
                'input' => [
                    'parent_id' => '10',
                    'service_account' => '123456',
                    'service_name' => 'SMS',
                    'incident_id' => null,
                    'user_id' => '50',
                    'reporter_id' => '5',
                    'service_messageid' => '1',
                    'message_detail' => null,
                    'message' => 'An SMS',
                    'message_date' => '2018-10-29',
                    'message_type' => '2',
                    'latitude' => null,
                    'longitude' => null
                ],
                'mockCalls' => [
                    'incident' => [],
                    'user' => ['50' => 75],
                    'reporter' => ['5' => 95],
                    'contact' =>  ['123456', 'sms', 95],
                    'parent' => ['10' => 100],
                ],
                'expected' => [
                    'parent_id' => 100,
                    'contact_id' => 95,
                    'post_id' => null,
                    'user_id' => 75,
                    'data_source' => null,
                    'data_source_message_id' => '1',
                    'title' => null,
                    'message' => 'An SMS',
                    'type' => 'sms',
                    'status' => 'sent',
                    'direction' => 'outgoing',
                    'additional_data' => null,
                ],
                'expectedDate' => new \DateTime('2018-10-29'),
            ],
            'message-with-location' => [
                'input' => [
                    'parent_id' => '10',
                    'service_account' => '123456',
                    'service_name' => 'SMS',
                    'incident_id' => null,
                    'user_id' => '50',
                    'reporter_id' => '5',
                    'service_messageid' => '1',
                    'message_detail' => null,
                    'message' => 'An SMS',
                    'message_date' => '2018-10-29',
                    'message_type' => '2',
                    'latitude' => '1.345',
                    'longitude' => '2.756'
                ],
                'mockCalls' => [
                    'incident' => [],
                    'user' => ['50' => 75],
                    'reporter' => ['5' => 95],
                    'contact' =>  ['123456', 'sms', 95],
                    'parent' => ['10' => 100],
                ],
                'expected' => [
                    'parent_id' => 100,
                    'contact_id' => 95,
                    'post_id' => null,
                    'user_id' => 75,
                    'data_source' => null,
                    'data_source_message_id' => '1',
                    'title' => null,
                    'message' => 'An SMS',
                    'type' => 'sms',
                    'status' => 'sent',
                    'direction' => 'outgoing',
                    'additional_data' => [
                        'location' => [[
                            'type' => 'Point',
                            'coordinates' => [2.756, 1.345],
                        ]]
                    ],
                ],
                'expectedDate' => new \DateTime('2018-10-29'),
            ],
        ];
    }
}
