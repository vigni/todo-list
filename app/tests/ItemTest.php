<?php

use App\Entity\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase {

    private $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->item = new Item("name", "content");
    }

    public function testIsValid()
    {
        $this->assertEquals("Item is valid!", $this->item->isValid());
    }

    public function testIsNotValidDueToEmptyName()
    {
        $this->item->setName("");
        $this->assertArrayHasKey("name", $this->item->isValid());
    }

    public function testIsNotValidDueToEmptyContent()
    {
        $this->item->setContent('');
        $this->assertArrayHasKey("content", $this->item->isValid());
    }

    public function testIsNotValidDueToContentToLong()
    {
        $this->item->setContent('');
        $this->assertArrayHasKey("content", $this->item->isValid());
    }

    public function testIsNotValueWithFullErrors()
    {
        $errorsExpected = [
            "name" => "Name empty",
            "content" => "Content invalid"
        ];

        $toLongText = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been 
        the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and 
        scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into 
        electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of 
        Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus 
        PageMaker including versions of Lorem Ipsum. Lorem Ipsum is simply dummy text of the printing and typesetting industry. 
        the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and 
        scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into 
        electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of 
        Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus 
        PageMaker including versions of Lorem Ipsum.";

        $this->item->setName("");
        $this->item->setContent($toLongText);
        $this->assertEquals($errorsExpected, $this->item->isValid());
    }
}