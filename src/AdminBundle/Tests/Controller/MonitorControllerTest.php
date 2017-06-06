<?php

namespace AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MonitorControllerTest extends WebTestCase
{
    public function testVermonitor()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'ver-monitor');
    }

}
