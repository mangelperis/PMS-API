<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use App\API\DTO\BookingDTO;
use App\Domain\Entity\Validation\BookingId;
use DateTime;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Booking implements BookingInterface
{
    #[Assert\Uuid]
    #[Assert\NotBlank]
    private UuidInterface $bookingId;
    #[Assert\Type(type: 'integer')]
    #[Assert\Choice(callback: [BookingId::class, 'getValidHotelId'])]
    #[Assert\NotBlank]
    private string $hotelId;
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    private string $locator;
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    private string $room;
    #[Assert\Date]
    #[Assert\NotBlank]
    private DateTime $checkIn;
    #[Assert\Date]
    #[Assert\NotBlank]
    private DateTime $checkOut;
    #[Assert\Type(type: 'array')]
    private array $guests;

    public function __construct(
        UuidInterface   $bookingId,
        string   $hotelId,
        string   $locator,
        string   $room,
        DateTime $checkIn,
        DateTime $checkOut,
        array    $guests
    )
    {
        $this->bookingId = $bookingId;
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
            $booking->bookingId,
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
     * @return array
     */
    public function getGuests(): array
    {
        return $this->guests;
    }

    /**
     * @return array
     */
    public function normalize(): array
    {
        return [
            'bookingId' => $this->getBookingId(),
            'hotel' => $this->getHotelId(), //TO DO ... transform
            'locator' => $this->getLocator(),
            'room' => $this->getRoom(),
            'checkIn' => $this->getCheckIn()->format('Y-m-d'),
            'checkOut' => $this->getCheckOut()->format('Y-m-d'),
            'numberOfNights' => $this->getNumberOfNights(),
            'totalPax' => count($this->getGuests()),
            'guests' => array_map(function (Guest $guest) {
                return [
                    'name' => $guest->getName(),
                    'lastname' => $guest->getLastname(),
                    'birthdate' => $guest->getBirthdate()->format('Y-m-d'),
                    'passport' => $guest->getPassport(),
                    'country' => $guest->getCountry(),
                ];
            }, $this->getGuests()),
        ];
    }

    /**
     * Get the number of nights for the booking.
     *
     * @return int
     */
    public function getNumberOfNights(): int
    {
        $checkIn = $this->getCheckIn();
        $checkOut = $this->getCheckOut();

        // Calculate the interval between checkIn and checkOut dates
        $interval = $checkOut->diff($checkIn);

        // Return the number of nights (days)
        return $interval->days;
    }

    /**
     * Create a Booking entity from a BookingDTO.
     *
     * @param BookingDTO $bookingDTO
     * @return Booking
     */
    public static function denormalize(BookingDTO $bookingDTO): Booking
    {
        $bookingId = Uuid::uuid4();

        return new self(
            $bookingId,
            $bookingDTO->getHotelId(),
            $bookingDTO->getLocator(),
            $bookingDTO->getRoom(),
            $bookingDTO->getCheckIn(),
            $bookingDTO->getCheckOut(),
            $bookingDTO->getGuests()
        );
    }
}





