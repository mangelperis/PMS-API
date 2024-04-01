<?php
declare(strict_types=1);


namespace App\Domain\Entity\Validation;

class BookingId
{
    //Example of valid hotel_id for validation, it could retrieve the values from somewhere else
    const VALID_HOTEL_ID = [
        "70ce8358-600a-4bad-8ee6-acf46e1fb8db",
        "3cbcd874-a7e0-4bb3-987e-eb36f05b7e7a",
        "ca385c3b-c2b1-4691-b433-c8cd51883d25",
        "5ab1d247-19ea-4850-9242-2d3ffbbdb58d"
    ];
    public static function getValidHotelId(): array
    {
        return self::VALID_HOTEL_ID;
    }
}