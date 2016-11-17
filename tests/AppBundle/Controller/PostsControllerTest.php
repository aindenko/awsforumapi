<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{

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

        $crawler = $client->request('GET', '/api/v1/posts',array(),array(),array('HTTP_X-Auth' => 'Hash'));

        echo  $client->getResponse()->getContent();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('status', $client->getResponse()->getContent());
    }



}
