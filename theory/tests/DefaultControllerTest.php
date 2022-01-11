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

    public function testLink(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'link');

        $this->assertResponseIsSuccessful();
        $link = $crawler->filter('body > a:contains("Login")')->link();

        $crawler = $client->click($link);
        $this->assertTrue((bool) $crawler->filter('label:contains("Keep me logged in")')->count());
    }

    ///public function testLogin(): void
    ///{
    ///    $client = static::createClient();
    ///    $crawler = $client->request('GET', 'login');
    ///
    ///    $form = $crawler->selectButton('login')->form();
    ///    $form['email'] = 'cepishmohnoril+1@gmail.com';
    ///    $form['password'] = '123qweqwe';
    ///    $crawler = $client->submit($form);
    ///
    ///    $crawler = $client->followRedirect();
    ///
    ///    $this->assertTrue((bool) $crawler->filter('a:contains("Logout")')->count());
    ///}

    /**
     * @dataProvider provideUrls
     */
    public function testRoute($url): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }

    public function provideUrls(): array
    {
        return [
            ['link'],
            ['param/doot'],
        ];
    }
}
