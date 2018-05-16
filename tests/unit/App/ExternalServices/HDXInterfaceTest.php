<?php

namespace Tests\Unit\App\ExternalServices;

use Ushahidi\App\ExternalServices\HDXInterface;

use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Except\ClientException;
use GuzzleHttp\Exception\RequestException;

class HDXInterfaceTest extends TestCase
{

    /* sample package/dataset data
        [
        'upload' => fopen(storage_path('app/catalogo-bibliotecas.csv'), 'r'),
        //  'mimetype' => 'text/csv',
        'package_id' => 'ckan-api-test-338',
        'name' => 'Buenos Aires - Bibliotecas',
        'format' => 'CSV',
        'description' => 'Listado con ubicación'
        ]*/

    public function testGetDatasetIDByTitle()
    {
        $exists_response = json_encode(['result'=>
                                            [ 'count' => 1,
                                              'results' => [['id' => 'knownid']]
                                            ]
                                      ]);
        $notexists_response = json_encode(['result'=> ['count' => 0,
                                                    'results' => [] ]]);

         $mock = new MockHandler([
             new Response(200, ['Content-Type' => 'application/json'], $exists_response),
             new Response(200, ['Content-Type' => 'application/json'], $notexists_response),
             new Response(500),
             new RequestException("Server unavailable", new Request('GET', 'test'))
         ]);

         $handler = HandlerStack::create($mock);
         $hdxInterface = new HDXInterface('test', 'test');
         $hdxInterface->setClientHandler($handler);

         $goodResult = $hdxInterface->getDatasetIDByTitle('test title');
         $notFoundResult = $hdxInterface->getDatasetIDByTitle('test title');
         $badResponse = $hdxInterface->getDatasetIDByTitle('test title');

         $this->assertEquals('knownid', $goodResult);
         $this->assertEquals(null, $notFoundResult);
         $this->assertEquals(null, $badResponse);
    }

    protected function getGoodCreateResponse()
    {
           $good_response =  [
            'help' => 'http://192.168.33.60:5000/api/3/action/help_show?name=package_create',
            'success' => 1,
            'result' => [
                    'license_title' => '',
                    'maintainer' => '',
                    'relationships_as_object' => [],
                    'private' => '',
                    'maintainer_email' =>'',
                    'num_tags' => 0,
                    'id' => 'b55a377d-5c8d-45fa-915b-fe008b2a851e',
                    'metadata_created' => '2018-05-16T18:21:14.790187',
                    'metadata_modified' => '2018-05-16T18:21:14.790193',
                    'author' => '',
                    'author_email' => '',
                    'state' => 'active',
                    'version' => '',
                    'creator_user_id' => 'a0f22b57-3ad6-484c-a48e-d55947288429',
                    'type' => 'dataset',
                    'resources' => [],
                    'num_resources' => 0,
                    'tags' => [],
                    'groups' => [],
                    'license_id' => '',
                    'relationships_as_subject' => [],
                    'organization' => [
                            'description' => 'This is a local org for testing',
                            'created' => '2018-05-12T04:57:57.903794',
                            'title' => 'UshahidiLocalOrg',
                            'name' => 'ushahidilocalorg',
                            'is_organization' => 1,
                            'state' => 'active',
                            'image_url' => '',
                            'revision_id' => 'bcd73b5a-8563-46e8-a140-f36e8cf797a2',
                            'type' => 'organization',
                            'id' => '98d635f0-e5c9-48f0-b2d3-871ccd5199a5',
                            'approval_status' => 'approved'
                        ],
                    'name' => 'somename',
                    'isopen' => '',
                    'url' => '',
                    'notes' => '',
                    'owner_org' => '98d635f0-e5c9-48f0-b2d3-871ccd5199a5',
                    'extras' => [],
                    'title' => '578',
                    'revision_id' => '39725bd3-e78f-40b0-b056-8911bbb6b21d'
                ]
            ];
                return $good_response;
    }


    public function testCreateNewDatasource()
    {
        $creation_success = json_encode($this->getGoodCreateResponse());
        $creation_failed = json_encode(['success' => '',
                                         'error' => [
                                            'message' =>
                                                'Access denied: User xxxx not authorized',
                                             '__type' => 'Authorization Error'
                                                ]
                                         ]);

         $fakeMetadata = [];  // can be empty for test
         $mock = new MockHandler([
             // $goodResult
             new Response(200, ['Content-Type' => 'application/json'], $creation_success),
             // $unauthorizedResult
             new Response(200, ['Content-Type' => 'application/json'], $creation_failed),
             // $badResponse
             new Response(500),
             new RequestException("Server unavailable", new Request('GET', 'test'))
         ]);

         $handler = HandlerStack::create($mock);
         $hdxInterface = new HDXInterface('test', 'test');
         $hdxInterface->setClientHandler($handler);

         $goodResult = $hdxInterface->createHDXDatasetRecord($fakeMetadata);
         $unauthorizedResult =$hdxInterface->createHDXDatasetRecord($fakeMetadata);
         $badResponse = $hdxInterface->createHDXDatasetRecord($fakeMetadata);

         $this->assertEquals('1', $goodResult['success']);
         $this->assertEquals('Access denied: User xxxx not authorized', $unauthorizedResult['error']['message']);
         $this->assertEquals(false, $badResponse);
    }
}
