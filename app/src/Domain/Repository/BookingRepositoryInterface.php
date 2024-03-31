<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Booking;

interface BookingRepositoryInterface
{
    public function save(Booking $booking): bool;
}