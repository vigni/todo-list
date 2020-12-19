<?php

use App\Entity\User;
use App\Entity\Item;
use PHPUnit\Framework\TestCase;

class TodoListTest extends TestCase {

    private $user;
    private $item;

    protected function setUp(): void
    {
        $this->item = new Item("test", "testcontent");
        $this->user = new User("lucas", "vigni", "email@gmail.com", new \DateTime("1998-06-20"), "correctpassword");
        parent::setUp();
    }

    public function testAddItemIsOk()
    {
        $this->assertEquals($this->user, $this->user->addItem($this->item));
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

}