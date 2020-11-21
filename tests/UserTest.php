<?php

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {

    private $user;

    protected function setUp(): void 
    {
        $this->user = new User("lucas", "vigni", "email@gmail.com", new \DateTime("1998-06-20"));
        parent::setUp();
    }

    public function testIsValidIsOk() 
    {
        $this->assertEquals(true, $this->user->isValid());
    }
    
    public function testIsNotValidDueToBirthday() 
    {
        $this->user->setBirthDate('test');
        $this->assertEquals(false, $this->user->isValid());
    }
}