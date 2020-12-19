<?php

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $user;

    protected function setUp(): void 
    {
        $this->user = new User("lucas", "vigni", "email@gmail.com", new \DateTime("1998-06-20"), "correctpassword");
        parent::setUp();
    }

    public function testIsValid()
    {
        $this->assertEquals("User is valid!", $this->user->isValid());
    }

    public function testIsNotValidDueToEmptyFirstName()
    {
        $this->user->setFirstName('');
        $this->assertArrayHasKey("firstName", $this->user->isValid());
    }

    public function testIsNotValidDueToEmptyLastName()
    {
        $this->user->setLastName('');
        $this->assertArrayHasKey("lastName", $this->user->isValid());
    }

    public function testIsNotValidDueToWrongEmail()
    {
        $this->user->setEmail('wrongemail');
        $this->assertArrayHasKey("email", $this->user->isValid());
    }

    public function testIsNotValidDueToShortPassword()
    {
        $this->user->setPassword('pwd');
        $this->assertArrayHasKey("password", $this->user->isValid());
    }

    public function testIsNotValidDueToLongPassword()
    {
        $this->user->setPassword('passwordpasswordpasswordpasswordpasswordpasswordpasswordpasswordpasswordpassword');
        $this->assertArrayHasKey("password", $this->user->isValid());
    }

    public function testIsNotValidDueToBirthDate()
    {
        $this->user->setBirthdate(new \DateTime("2010-06-20"));
        $this->assertArrayHasKey("birthdate", $this->user->isValid());
    }

    public function testIsNotValidWithFullError()
    {
        $invalidUser= new User("", "", "email.com", new \DateTime("2015-06-20"), "pwd");
        $errorsExpected = [
            "firstName" => "Firstname empty",
            "lastName" => "Lastname empty",
            "email" => "Email invalid",
            "password" => "Password invalid",
            "birthdate" => "User to young"
        ];

        $this->assertEquals($errorsExpected, $invalidUser->isValid());
    }
}