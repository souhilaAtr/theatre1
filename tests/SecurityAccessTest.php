<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityAccessTest extends WebTestCase
{
    public function testAcceessNewEvenet(): void
    {
        $client = static::createClient();
        $client->request('GET', '/evenement/new');

       $this->assertResponseRedirects('/login');
        
    }
    public function testAccessNewCategorie(){
        $client = static::createClient();
        $client->request('GET', 'categorie/new');
        $this->assertResponseRedirects('/login');
    }
}
