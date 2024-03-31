<?php
declare(strict_types=1);


namespace App\Infrastructure\Adapter;

use DateTime;

class PMSBookingDTO
{
    private string $hotelId;
    private string $hotelName;
    private array $guest;
    private array $booking;
    private DateTime $created;
    private string $signature;

    public function __construct(
        string $hotelId,
        string $hotelName,
        array $guest,
        array $booking,
        DateTime $created,
        string $signature
    ) {
        $this->hotelId = $hotelId;
        $this->hotelName = $hotelName;
        $this->guest = $guest;
        $this->booking = $booking;
        $this->created = $created;
        $this->signature = $signature;
    }

    public function getHotelId(): string
    {
        return $this->hotelId;
    }

    public function getHotelName(): string
    {
        return $this->hotelName;
    }

    public function getGuest(): array
    {
        return $this->guest;
    }

    public function getBooking(): array
    {
        return $this->booking;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getSignature(): string
    {
        return $this->signature;
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'hotel_id' => $this->hotelId,
            'hotel_name' => $this->hotelName,
            'guest' => $this->guest,
            'booking' => $this->booking,
            'created' => $this->created,
            'signature' => $this->signature,
        ];
    }
}