<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator\Constraints\ValidDateRange;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_USER')"},
 *     itemOperations={
 *          "put"={"security"="is_granted('ROLE_USER') and user == object.getUser()", "security_message"="Only the creators can edit a trip"},
*           "patch"={"security"="is_granted('ROLE_USER') and user == object.getUser()", "security_message"="Only the creators can edit a trip"},
 *          "get"={"security"="is_granted('ROLE_USER') and user == object.getUser()"},
 *          "delete"={"security"="is_granted('ROLE_USER') and user == object.getUser()", "security_message"="Only creator can delete the trip"}
 *     },
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TripRepository")
 * @ApiFilter(SearchFilter::class, properties={"country"})
 * @ApiFilter(DateFilter::class, properties={"startDate", "endDate"})
 * @ValidDateRange()
 */
class Trip
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan(propertyPath="startDate", message="End date cannot be before start date")
     */
    private $endDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity="Country", cascade={"persist"})
     * @Assert\NotBlank(message="The 'country' is required!")
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="trips")
     */
    private $user;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country): void
    {
        $this->country = $country;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }
}
