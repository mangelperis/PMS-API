<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Table(name: 'guests')]
#[ORM\UniqueConstraint(name: 'passport', columns: ['passport'])]
class Guest
{
    #[Assert\Type(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private string $name;
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private string $lastname;
    #[Assert\Type(type: 'date')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'date')]
    private DateTime $birthdate;
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 13)]
    private string $passport;
    #[Assert\Country]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 2)]
    private string $country;

    #[ORM\Column(name: 'created', type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $created;
    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTime $updated;


    #[Assert\NotBlank]
    #[ORM\ManyToOne(targetEntity: 'App\Domain\Entity\Booking', inversedBy: 'guests')]
    #[ORM\JoinColumn(name: 'booking_id', referencedColumnName: 'id')]
    private Booking $booking;

    public function __construct(
        string $name,
        string $lastname,
        DateTime $birthdate,
        string $passport,
        string $country
    ) {
        $this->name = $name;
        $this->lastname = $lastname;
        $this->birthdate = $birthdate;
        $this->passport = $passport;
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return DateTime
     */
    public function getBirthdate(): DateTime
    {
        return $this->birthdate;
    }

    /**
     * @return string
     */
    public function getPassport(): string
    {
        return $this->passport;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Booking
     */
    public function getBooking(): Booking
    {
        return $this->booking;
    }

    /**
     * @param int $bookingId
     * @return void
     */
    public function setBooking(Booking $booking): void
    {
        $this->booking = $booking;
    }

    /**
     * @param DateTime $created
     * @return void
     */
    public function setCreated(DateTime $created):void
    {
        $this->created = $created;
    }

}