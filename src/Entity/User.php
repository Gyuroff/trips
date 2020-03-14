<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\AuthController;
use App\Controller\TokenController;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"user:write"}},
 *     attributes={"security"="is_granted('ROLE_USER')"},
 *     itemOperations={
 *          "get",
 *          "put"={"security"="(is_granted('ROLE_USER') and user == object) or is_granted('ROLE_ADMIN')", "security_message"="You can edit only your own user"},
 *          "delete"={"security"="(is_granted('ROLE_USER') and object == user) or is_granted('ROLE_ADMIN')", "security_message"="You can delete only your own user"}
 *     },
 *     collectionOperations={
 *          "get",
 *          "create_user" = {
 *              "method"="POST",
 *              "path"="/register",
 *              "controller"=AuthController::class,
 *              "openapi_context"= {
 *                  "summary"="Register user",
 *                  "responses": {
 *                      "200": {
 *                          "description": "Rergister user response",
 *                          "schema": {
 *                              "type": "string"
 *                          }
 *                      }
 *                  },
 *                  "parameters": {
 *                      {
 *                          "name": "email",
 *                          "in": "body",
 *                          "description": "Email of the user",
 *                          "required": true,
 *                          "type": "string"
 *                      },
 *                      {
 *                          "name": "username",
 *                          "in": "body",
 *                          "description": "Username of the user",
 *                          "required": true,
 *                          "type": "string"
 *                      },
 *                      {
 *                          "name": "password",
 *                          "in": "body",
 *                          "description": "Password of the user",
 *                          "required": true,
 *                          "type": "string"
 *                      }
 *                  }
 *              }
 *          },
 *          "get_token" = {
 *              "method"="POST",
 *              "path"="/login-check",
 *              "controller"=TokenController::class,
 *              "openapi_context"= {
 *                  "summary"="Retrieve token",
 *                  "responses": {
 *                      "200": {
 *                          "description": "token!",
 *                          "schema": {
 *                              "type": "string"
 *                          }
 *                      }
 *                  },
 *                  "parameters": {
 *                      {
 *                          "name": "username",
 *                          "in": "body",
 *                          "description": "Username of the user",
 *                          "required": true,
 *                          "type": "string"
 *                      },
 *                      {
 *                          "name": "password",
 *                          "in": "body",
 *                          "description": "Password of the user",
 *                          "required": true,
 *                          "type": "string"
 *                      }
 *                  }
 *              }
 *          }
 *     }
 * )
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user:write"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user:read", "user:write"})
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity="Trip", mappedBy="user")
     */
    private $trips;

    public function __construct()
    {
        $this->trips = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|Trip[]
     */
    public function getTrips(): Collection
    {
        return $this->trips;
    }

    public function addTrip(Trip $trip): self
    {
        if (!$this->trips->contains($trip)) {
            $this->trips[] = $trip;
        }

        return $this;
    }

    public function removeTrip(Trip $trip): self
    {
        if ($this->trips->contains($trip)) {
            $this->trips->removeElement($trip);
        }

        return $this;
    }
}
