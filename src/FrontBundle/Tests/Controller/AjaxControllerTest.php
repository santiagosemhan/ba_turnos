<?php

namespace FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AjaxControllerTest extends WebTestCase
{
    public function testGettipotramite()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'get_tipo_tramite');
    }

}
