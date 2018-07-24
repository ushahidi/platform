<?php
namespace Tests\Unit\App\ExternalServices;

use Germanazo\CkanApi\Repositories\LicenseRepository;
use Ushahidi\App\ExternalServices\HDXInterface;

use Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Except\ClientException;
use GuzzleHttp\Exception\RequestException;
use Ushahidi\App\Repository\HXL\HXLLicenseRepository;
use Ushahidi\Core\Entity\HXL\HXLLicense;

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

    public function testGetDatasetIDByName()
    {
        $exists_response = json_encode(['result'=>
                                            [ 'id' => 'knownid']
                                      ]);
        $notexists_response = json_encode(['result'=> []]);

         $mock = new MockHandler([
             new Response(200, ['Content-Type' => 'application/json'], $exists_response),
             new Response(200, ['Content-Type' => 'application/json'], $notexists_response),
             new Response(500),
             new RequestException("Server unavailable", new Request('GET', 'test'))
         ]);

         $handler = HandlerStack::create($mock);
         $hdxInterface = new HDXInterface('test', 'test', 'maintainer-1234');
         $hdxInterface->setClientHandler($handler);

         $goodResult = $hdxInterface->getDatasetIDByName('test title', 'my org');
         $notFoundResult = $hdxInterface->getDatasetIDByName('test title', 'my org2');
         $badResponse = $hdxInterface->getDatasetIDByName('test title', 'my bad org');

         $this->assertEquals('knownid', $goodResult);
         $this->assertEquals(null, $notFoundResult);
         $this->assertEquals(null, $badResponse);
    }
    public function testSlugIsFormatted()
    {

        $metadata = [
            "maintainer" => "maintainer-1",
            "organisation_id" => "org-id-1",
            "organisation_name" => "org-name",
            "private" => "private",
            "dataset_title" => "cuantos posts hay por año?",
            "source" => "source"
        ];
        $tags = [
            ["name" => "coordinates"]
        ];
        $license = new \Ushahidi\Core\Entity\HXL\HXLLicense([
            'code' => "ushahidi".rand(),
            'name' => "ushahidi-dataset",
            'link' => "other",
        ]);

        $dataset = [
            "name" =>  $metadata["dataset_title"],
            "author" => $metadata['maintainer'],
            "maintainer" => $metadata['maintainer'],
            "organization" => $metadata['organisation_id'],
            "private" => $metadata['private'],
            "owner_org" => $metadata['organisation_id'],
            "title" => $metadata['dataset_title'],
            "dataset_source" =>  $metadata['source'],
            "data_update_frequency" => "1", //1 day. TODO add frequency to metadata
            "methodology" => "other", //TODO add methodology to metadata
            "tags" => $tags, //[{"name":"coordinates"}],
            "license_id" => $license->code,
            "allow_no_resources" => true
        ];

        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($dataset))
        ]);
        $handler = HandlerStack::create($mock);
        $hdxInterface = new HDXInterface('test', 'test', 'maintainer-1234');
        $hdxInterface->setClientHandler($handler);

        $good = $hdxInterface->formatDatasetObject($metadata, $license, $tags);
        $this->assertEquals("org-name-cuantos-posts-hay-por-ano", $good["name"]);

        $metadata["dataset_title"] = null;
        $this->expectExceptionMessage("Cannot create a slug without an organisation name and dataset title");
        $hdxInterface->formatDatasetObject($metadata, $license, $tags);

        $metadata["dataset_title"] = "something";
        $metadata["organisation_name"] = "some-org";
        $metadata["organisation_id"] = "id-of-some-org";
        $this->expectExceptionMessage("Cannot create a slug without an organisation name and dataset title");
        $hdxInterface->formatDatasetObject($metadata, $license, $tags);
    }
    public function testGetAllOrganizationsForUser()
    {
        $auth_failed_response = json_encode(['success' => '',
                                            'error' => [
                                            'message' =>
                                            'Access denied: User xxxx not authorized',
                                            '__type' => 'Authorization Error'
                                            ]
                                         ]);

         $fakeMetadata = [];  // can be empty for test
         $mock = new MockHandler([
             // $goodResult
             new Response(
                 200,
                 ['Content-Type' => 'application/json'],
                 json_encode($this->getGoodOrganizationsResponse())
             ),
             // $unauthorizedResult
             new Response(200, ['Content-Type' => 'application/json'], $auth_failed_response),
             // $badResponse
             new Response(500),
             new RequestException("Server unavailable", new Request('GET', 'test'))
         ]);

         $handler = HandlerStack::create($mock);
         $hdxInterface = new HDXInterface('test', 'test', 'test');
         $hdxInterface->setClientHandler($handler);

         $goodResult = $hdxInterface->getAllOrganizationsForUser();
         $unauthorizedResult =$hdxInterface->getAllOrganizationsForUser();
         $badResponse = $hdxInterface->getAllOrganizationsForUser();

         $this->assertEquals(1, count($goodResult));
         $this->assertEquals(false, $unauthorizedResult);
         $this->assertEquals(false, $badResponse);
    }

    protected function getGoodOrganizationsResponse()
    {
        $response = [
        'help' => 'http://localhost/api/3/action/help_show?name=organization_list',
        'success' => 1,
        'result' => [  [
                        'display_name' => 'UshahidiLocalOrg',
                        'description' => 'This is a local org for testing',
                        'image_display_url' => '',
                        'package_count' => 32,
                        'created' => '2018-05-12T04:57:57.903794',
                        'name' => 'ushahidilocalorg',
                        'is_organization' => 1,
                        'state' => 'active',
                        'image_url' => '',
                        'type' => 'organization',
                        'title' => 'UshahidiLocalOrg',
                        'revision_id' => 'bcd73b5a-8563-46e8-a140-f36e8cf797a2',
                        'num_followers' => 0,
                        'id' => '98d635f0-e5c9-48f0-b2d3-871ccd5199a5',
                        'approval_status' => 'approved',
                   ]
            ]
        ];
        return $response;
    }

    protected function getGoodCreateResponse()
    {
           $good_response =  [
            'help' => 'http://192.168.33.60:5000/api/3/action/help_show?name=package_create',
            'success' => 1,
            'result' => [
                    'license_title' => '',
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
}
