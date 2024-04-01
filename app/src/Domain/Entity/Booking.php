<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use App\Domain\Entity\Validation\BookingId;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Domain\Repository\BookingRepository')]
#[ORM\Table(name: 'bookings')]
#[ORM\UniqueConstraint(name: 'booking_id', columns: ['booking_id'])]
#[ORM\Index(name: 'hotel_id', columns: ['hotel_id'])]
#[ORM\Index(name: 'locator', columns: ['locator'])]
class Booking implements BookingInterface
{

    #[Assert\Type(type: 'integer')]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'uuid')]
    private UuidInterface $bookingId;
    #[Assert\Type(type: 'integer')]
    #[Assert\Choice(callback: [BookingId::class, 'getValidHotelId'])]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private string $hotelId;
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private string $locator;
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private string $room;
    #[Assert\Date]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'datetime')]
    private DateTime $checkIn;
    #[Assert\Date]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'datetime')]
    private DateTime $checkOut;

    /**
     * @var ArrayCollection<Guest>
     */
    #[Assert\Type(type: 'array')]
    #[ORM\OneToMany(targetEntity: 'App\Domain\Entity\Guest', mappedBy: 'booking', cascade:["persist", "remove"])]
    private ArrayCollection $guests;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'created', type: Types::DATETIME_MUTABLE)]
    private DateTime $created;
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $updated;

    public function __construct(
        string     $hotelId,
        string     $locator,
        string     $room,
        DateTime   $checkIn,
        DateTime   $checkOut,
        ArrayCollection $guests
    )
    {
        $this->bookingId = Uuid::uuid4();
        $this->hotelId = $hotelId;
        $this->locator = $locator;
        $this->room = $room;
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
        $this->guests = $guests;
    }

    /**
     * @param object $booking
     * @return self
     * @throws \Exception
     */
    public static function createFromObject(object $booking): self
    {
        return new self(
            $booking->hotelId,
            $booking->locator,
            $booking->room,
            new DateTime($booking->checkIn),
            new DateTime($booking->checkOut),
            $booking->guests
        );
    }

    /**
     * @return UuidInterface
     */
    public function getBookingId(): UuidInterface
    {
        return $this->bookingId;
    }

    /**
     * @return string
     */
    public function getHotelId(): string
    {
        return $this->hotelId;
    }

    /**
     * @return string
     */
    public function getLocator(): string
    {
        return $this->locator;
    }

    /**
     * @return string
     */
    public function getRoom(): string
    {
        return $this->room;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCheckIn(): \DateTimeInterface
    {
        return $this->checkIn;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCheckOut(): \DateTimeInterface
    {
        return $this->checkOut;
    }

    /**
     * @return Collection
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setCreated(DateTime $created):void
    {
        $this->created = $created;
    }
}





