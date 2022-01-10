<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testParam(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/param/doot');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Hello doot');
    }
}
