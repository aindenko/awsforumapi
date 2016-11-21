<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthCheckControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/v1/_healthcheck');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('status', $client->getResponse()->getContent());
    }
}
