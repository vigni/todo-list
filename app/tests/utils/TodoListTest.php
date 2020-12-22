<?php

use App\Entity\User;
use App\Entity\Item;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Carbon\Carbon;

class TodoListTest extends TestCase {

    private $user;
    private $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->item = new Item("test", "testcontent");
        $this->item->setCreatedAt(Carbon::now());

        $this->user = new User();

        $this->user = $this->getMockBuilder(User::class)
            ->setMethods(['getCountItems', 'getItems'])
            ->getMock()
            ->setFirstName("lucas")
            ->setLastName("vigni")
            ->setBirthdate(new \DateTime("1998-06-20"))
            ->setEmail("email@gmail.com")
            ->setPassword("correctpassword");
    }

    public function testAddItemIsOk()
    {
        $this->assertEquals($this->item, $this->user->addItem($this->item));
    }

    public function testAddItemIsNotOkDueToInvalidItem()
    {
        $this->item->setContent("");
        $this->assertEquals(["Item invalid !"], $this->user->addItem($this->item));
    }

    public function testAddItemIsNotOkDueToInvalidUser()
    {
        $this->user->setFirstName("");
        $this->assertEquals(["User invalid !"], $this->user->addItem($this->item));
    }

    public function testAddItemIsNotOkDueToFullyTodoList()
    {
        $this->user->expects($this->any())->method('getCountItems')->willReturn(11);
        $this->assertEquals(["Todolist is already full"], $this->user->addItem($this->item));
    }

    public function testAddItemIsOkWithMailPrevention()
    {
        $this->user->expects($this->any())->method('getCountItems')->willReturn(7);
        $this->assertEquals("Email has been send: (You only have 2 items left !)", $this->user->addItem($this->item));
    }

    public function testAddItemIsNotOkDueTo30Mins()
    {
        $itemMock = new Item("testMock", "testcontentmock");
        $itemMock->setCreatedAt(Carbon::now()->subMinutes(25));
        $this->user->expects($this->any())->method('getItems')->willReturn(new ArrayCollection([$itemMock]));

        $this->assertEquals(["You have to wait 30 minutes between each item's creation"], $this->user->addItem($this->item));
    }
}