<?php

namespace App\Domain\Entity;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

interface BookingInterface
{
    public function getBookingId(): UuidInterface;

    public function getHotelId(): string;

    public function getLocator(): string;

    public function getRoom(): string;

    public function getCheckIn(): DateTimeInterface;

    public function getCheckOut(): DateTimeInterface;

    public function getGuests(): array;

}