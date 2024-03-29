<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use App\Domain\Entity\Validation\BookingId;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class Booking
{
    #[Assert\Uuid]
    #[Assert\NotBlank]
    private string $bookingId;
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
        string   $bookingId,
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
     * @return string
     */
    public function getBookingId(): string
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
}





