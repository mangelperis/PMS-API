<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use App\Domain\Entity\Validation\BookingId;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Domain\Repository\BookingRepository')]
#[ORM\Table(name: 'bookings')]
#[ORM\UniqueConstraint(name: 'booking_id', columns: ['booking_id'])]
#[ORM\Index(columns: ['hotel_id'], name: 'hotel_id')]
#[ORM\Index(columns: ['locator'], name: 'locator')]
#[ORM\Index(columns: ['room'], name: 'room')]
class Booking implements BookingInterface
{

    #[Assert\Type(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;
    #[Assert\Uuid]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'uuid')]
    private UuidInterface $bookingId;
    #[Assert\Type(type: 'string')]
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
    #[Assert\Type(type: 'datetime')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'datetime')]
    private DateTime $checkIn;
    #[Assert\Type(type: 'datetime')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'datetime')]
    private DateTime $checkOut;

    /**
     * @var ArrayCollection<Guest>
     */
    #[Assert\Type(type: 'object')]
    #[ORM\OneToMany(mappedBy: 'booking', targetEntity: 'App\Domain\Entity\Guest', cascade: ['persist', 'remove'])]
    private Collection $guests;

    #[ORM\Column(name: 'created', type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private DateTime $created;
    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTime $updated;

    public function __construct(
        string          $hotelId,
        string          $locator,
        string          $room,
        DateTime        $checkIn,
        DateTime        $checkOut
    )
    {
        $this->bookingId = Uuid::uuid4();
        $this->hotelId = $hotelId;
        $this->locator = $locator;
        $this->room = $room;
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
        $this->guests = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
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



    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function addGuest(Guest $guest): void
    {
        $guest->setBooking($this);
        $this->guests->add($guest);
    }
}





