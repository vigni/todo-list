<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Null_;
use App\Service\EmailService;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @ApiResource(
 *     collectionOperations={"get"={"normalization_context"={"groups"="conference:list"}}},
 * )
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $last_name;

    /**
     * @ORM\Column(type="date")
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=Item::class, mappedBy="user", orphanRemoval=true)
     */
    private $items;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    
    public function isValid()
    {
        $errors = [];
        if (empty($this->first_name)) {
            $errors["firstName"] = "Firstname empty";
        }
        if (empty($this->last_name)) {
            $errors["lastName"] = "Lastname empty";
        }
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Email invalid";
        }
        if (strlen($this->password) <= 8 || strlen($this->password) >= 40) {
            $errors["password"] = "Password invalid";
        }
        if($this->birthdate->diff(new \DateTime())->format('%y') <= 13){
            $errors["birthdate"] = "User to young";
        }
        if (!empty($errors)) {
            return $errors;
        }
        else {
            return "User is valid!";
        }
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getCountItems(): int
    {
        return count($this->getItems());
    }

    public function addItem(Item $item)
    {
        $errors = [];

        if($item->isValid() !== "Item is valid!") {
            array_push($errors,"Item invalid !");
        }

        if($this->isValid() !== "User is valid!") {
            array_push($errors,"User invalid !");
        }
        if($this->getCountItems() >= 10) {
            array_push($errors,"Todolist is already full");
        }

        if(count($this->getItems()) !== 0)
        {
            $today = Carbon::now();
            $lastItemDate = Carbon::parse($this->getItems()[0]->getCreatedAt());
            $diffMins = $today->diffInMinutes($lastItemDate);
            if ($diffMins < 30) {
                array_push($errors,"You have to wait 30 minutes between each item's creation");
            }
        }

        if (!empty($errors)) {
            return $errors;
        } else {
            $item->setUser($this);
        }

        if ($this->getCountItems()  == 7) {
            $emailService = new EmailService();
            return $emailService->sendMail();
        }

        return $item;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getUser() === $this) {
                $item->setUser(null);
            }
        }

        return $this;
    }
}
