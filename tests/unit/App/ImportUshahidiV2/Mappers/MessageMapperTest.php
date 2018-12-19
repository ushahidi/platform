<?php

namespace Tests\Unit\App\ImportUshahidiV2\Mappers;

use Ushahidi\App\ImportUshahidiV2\Mappers\MessageMapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;
use Ushahidi\Core\Entity\Message;
use Ushahidi\Core\Entity\Contact;
use Ushahidi\Core\Entity\ContactRepository;
use Tests\TestCase;
use Mockery as M;
use Faker;

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

        $mappingRepo = M::mock(ImportMappingRepository::class);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'incident', $mockCalls['incident'][0])
            ->andReturn($mockCalls['incident'][1]);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'user', $mockCalls['user'][0])
            ->andReturn($mockCalls['user'][1]);
        $mappingRepo->shouldReceive('getDestId')
            ->with($importId, 'message', $mockCalls['parent'][0])
            ->andReturn($mockCalls['parent'][1]);

        $contactRepo = M::mock(ContactRepository::class);
        $contactRepo->shouldReceive('getByContact')
            ->with($mockCalls['contact'][0], $mockCalls['contact'][1])
            ->andReturn(new Contact(['id' => $mockCalls['contact'][2]]));

        $mapper = new MessageMapper($mappingRepo, $contactRepo);

        $message = $mapper($importId, $input);

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
                    'service_messageid' => 'absdflkajsdf',
                    'message_detail' => 'A message',
                    'message' => 'Some subject',
                    'message_date' => '2018-10-30',
                    'message_type' => '1',
                    'latitude' => null,
                    'longitude' => null
                ],
                'mockCalls' => [
                    'incident' => [5, 55],
                    'user' => [7, 77],
                    'contact' =>  ['jim@jimssite.test', 'email', 99],
                    'parent' => [null, null],
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
                    'service_messageid' => '1',
                    'message_detail' => null,
                    'message' => 'An SMS',
                    'message_date' => '2018-10-29',
                    'message_type' => '2',
                    'latitude' => null,
                    'longitude' => null
                ],
                'mockCalls' => [
                    'incident' => [null, null],
                    'user' => [50, 75],
                    'contact' =>  ['123456', 'sms', 95],
                    'parent' => [10, 100],
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
                    'service_messageid' => '1',
                    'message_detail' => null,
                    'message' => 'An SMS',
                    'message_date' => '2018-10-29',
                    'message_type' => '2',
                    'latitude' => '1.345',
                    'longitude' => '2.756'
                ],
                'mockCalls' => [
                    'incident' => [null, null],
                    'user' => [50, 75],
                    'contact' =>  ['123456', 'sms', 95],
                    'parent' => [10, 100],
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
