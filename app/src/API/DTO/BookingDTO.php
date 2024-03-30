<?php
declare(strict_types=1);


namespace App\API\DTO;

use DateTime;
use DateTimeInterface;

class BookingDTO
{
    private string $bookingId;
    private string $hotelId;
    private string $locator;
    private string $room;
    private DateTime $checkIn;
    private DateTime $checkOut;
    private array $guests;

    public function getBookingId(): string
    {
        return $this->bookingId;
    }

    public function setBookingId(string $bookingId): void
    {
        $this->bookingId = $bookingId;
    }

    public function getHotelId(): string
    {
        return $this->hotelId;
    }

    public function setHotelId(string $hotelId): void
    {
        $this->hotelId = $hotelId;
    }

    public function getLocator(): string
    {
        return $this->locator;
    }

    public function setLocator(string $locator): void
    {
        $this->locator = $locator;
    }

    public function getRoom(): string
    {
        return $this->room;
    }

    public function setRoom(string $room): void
    {
        $this->room = $room;
    }

    public function getCheckIn(): DateTime
    {
        return $this->checkIn;
    }

    public function setCheckIn(DateTime $checkIn): void
    {
        $this->checkIn = $checkIn;
    }

    public function getCheckOut(): DateTime
    {
        return $this->checkOut;
    }

    public function setCheckOut(DateTime $checkOut): void
    {
        $this->checkOut = $checkOut;
    }

    public function getGuests(): array
    {
        return $this->guests;
    }

    public function setGuests(array $guests): void
    {
        $this->guests = $guests;
    }
}