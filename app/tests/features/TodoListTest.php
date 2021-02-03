<?php
namespace App\tests\features;

use App\Entity\User;
use App\Entity\Item;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodoListTest extends WebTestCase
{
    private $itemRepo;
    private $userRepo;
    private $client;
    private $em;
    private $userId;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $kernel = static::bootKernel();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->itemRepo = $this->em->getRepository(\App\Entity\Item::class);
        $this->userRepo = $this->em->getRepository(\App\Entity\User::class);
        $this->userId = $this->userRepo->findAll()[0]->getId();

        parent::setUp();
    }

    public function testNewItemUserIsOk()
    {
        //TODO : reload fixtures at the end
        $allItems = $this->itemRepo->findAll();

        foreach ($allItems as $i)
        {
            $this->em->remove($i);
        }
        $this->em->flush();

        $data = array
        (
            "name"=> "DM",
            "content"=> "Math",
        );

        $value = "/api/users/" . $this->userId;
        $expectedCount = count($this->itemRepo->findAll()) + 1;

        $this->client->request('POST', '/item/user/' . $this->userId . '/new', $data);

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertContains($value, $content, "testArray doesn't contains value as value");
        $this->assertEquals($expectedCount, count($this->itemRepo->findAll()));

    }

    public function testNewItemUserNotFound()
    {
        $data = array
        (
            "name"=> "DM",
            "content"=> "Math",
        );

        $this->client->request('POST', '/item/user/999/new', $data);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("User doesn't exist !", $content);
    }

    public function testNewItemUserWrongItem()
    {
        $data = array
        (
            "name"=> "",
            "content"=> "Math",
        );

        $this->client->request('POST', '/item/user/'. $this->userId .'/new', $data);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString("Item invalid !", $content, "testArray doesn't contains value as value");

    }
}