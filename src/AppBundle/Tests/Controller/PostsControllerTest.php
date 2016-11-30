<?php

namespace Tests\AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{

    public function setUp()
    {
        $this->loadFixtures(array(
            'AppBundle\Tests\Fixtures\AppExistsPostsFixture',
        ));
    }

    public function test401()
    {
        $endpoints = array(
            array('GET','/api/v1/posts'),
            array('POST','/api/v1/posts'),
            array('GET','/api/v1/posts/1'),
            array('GET','/api/v1/posts/export'),
            array('GET','/api/v1/posts/1'),
        );
        foreach ($endpoints as $endpoint) {
            $client = static::createClient();
            $crawler = $client->request($endpoint[0],$endpoint[1]);

            $this->assertEquals(401, $client->getResponse()->getStatusCode());
        }
    }

    public function testGetPosts()
    {
        $client = static::createClient();

        $expextedRes  = <<<EOT
{"_metadata":{"totalCount":2,"limit":10,"offset":0,"totalViews":"22"},"posts":[{"id":1,"title":"Test title","image_thumb_url":"http:\/\/example.com\/1_thumb.jpg","created_at":"2016-01-01T00:00:00+0000"},{"id":2,"title":"Test title","image_thumb_url":"http:\/\/example.com\/2_thumb.jpg","created_at":"2016-01-01T00:00:00+0000"}]}
EOT;

        $crawler = $client->request('GET', '/api/v1/posts',array(),array(),array('HTTP_X-Auth' => 'Hash'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($expextedRes, $client->getResponse()->getContent());
    }



}
