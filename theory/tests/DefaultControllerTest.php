<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    private $em;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        if (empty($this->client)) {
            $this->client = static::createClient();
        }

        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        //$this->em->rollback(); // Generates error for some reason

        //It seems in Symfony5 trick with transaction is done by separate bundle
        //https://symfony.com/doc/current/testing.html#resetting-the-database-automatically-before-each-test

        $this->em->close();
        $this->em = null;
    }

    public function testParam(): void
    {
        $crawler = $this->client->request('GET', '/param/doot');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Hello doot');
    }

    public function testLink(): void
    {
        $crawler = $this->client->request('GET', 'link');

        $this->assertResponseIsSuccessful();
        $link = $crawler->filter('body > a:contains("Login")')->link();

        $crawler = $this->client->click($link);
        $this->assertTrue((bool) $crawler->filter('label:contains("Keep me logged in")')->count());
    }

    ///public function testLogin(): void
    ///{
    ///    $crawler = $this->client->request('GET', 'login');
    ///
    ///    $form = $crawler->selectButton('login')->form();
    ///    $form['email'] = 'cepishmohnoril+1@gmail.com';
    ///    $form['password'] = '123qweqwe';
    ///    $crawler = $this->client->submit($form);
    ///
    ///    $crawler = $this->client->followRedirect();
    ///
    ///    $this->assertTrue((bool) $crawler->filter('a:contains("Logout")')->count());
    ///}

    /**
     * @dataProvider provideUrls
     */
    public function testRoute($url): void
    {
        $crawler = $this->client->request('GET', $url);
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
