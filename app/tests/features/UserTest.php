<?php
namespace App\tests\features;

use App\Entity\User;
use App\Entity\Item;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Csrf\CsrfToken;

class UserTest extends WebTestCase
{
    private $userRepo;
    private $client;
    private $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $kernel = static::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->userRepo = $this->em->getRepository(\App\Entity\User::class);

        parent::setUp();
    }

    public function testGetAllUserIsOk()
    {
        $expectedCount = count($this->userRepo->findAll());

        $this->client->request('GET', '/user/list');

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedCount, $content);
    }


    public function testGetUserIsOk()
    {
        $expectedId = $this->userRepo->findAll()[0]->getId();

        $this->client->request('GET', '/user/' . $expectedId);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedId, $content);
    }

    public function testGetUserUnknown()
    {

        $this->client->request('GET', '/user/999');

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("User doesn't exist !", $content);
    }

    public function testNewUserIsOk()
    {

        $data = array
        (
            "first_name"=> "Nick",
            "last_name"=> "Dupont",
            "birthdate"=> "20-06-1990",
            "password" => "passworddenick",
            "email" => "nickpont@gmail.com"

        );

        $expectedCount = count($this->userRepo->findAll()) + 1;

        $this->client->request('POST', '/user/new', $data);

        $response = $this->client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($expectedCount, count($this->userRepo->findAll()));
    }

    public function testUpdateUserIsOk()
    {
        $data = array
        (
            "first_name"=> "Benoit",
            "last_name"=> "Dupont",
            "birthdate"=> "20-06-1990",
            "password" => "passworddenick",
            "email" => "benoit@gmail.com"
        );
        $userId = $this->userRepo->findBy(["first_name" => "Nick"])[0]->getId();
        $value = "Benoit";

        $this->client->request('PUT', '/user/' . $userId . '/edit', $data);

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains($value, $content, "testArray doesn't contains value as value");
    }

    public function testUpdateUserUnknown()
    {
        $data = array
        (
            "first_name"=> "Benoit",
            "last_name"=> "Dupont",
            "birthdate"=> "20-06-1990",
            "password" => "passworddenick",
            "email" => "benoit@gmail.com"
        );

        $this->client->request('PUT', '/user/999/edit', $data);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("User doesn't exist !", $content);
    }

    public function testDeleteUserIsOk()
    {
        $userId = $this->userRepo->findBy(["first_name" => "Benoit"])[0]->getId();

        $this->client->request('DELETE', '/user/delete/' . $userId);

        $response = $this->client->getResponse();

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function testDeleteUserUnknown()
    {
        $this->client->request('DELETE', '/user/delete/999');

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("User doesn't exist !", $content);
    }
}