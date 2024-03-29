<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use DateTime;

class Booking
{
    private string $bookingId;
    private string $hotelId;
    private string $locator;
    private string $room;
    private DateTime $checkIn;
    private DateTime $checkOut;
    private array $guests;

    // Constructor, getters y setters
}