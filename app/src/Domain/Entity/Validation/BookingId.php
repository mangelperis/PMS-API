<?php
declare(strict_types=1);


namespace App\Domain\Entity\Validation;

class BookingId
{
    //Example of valid hotel_id for validation, it could retrieve the values from somewhere else
    const VALID_HOTEL_ID = ["36001", "28001", "28003","49001"];
    public static function getValidHotelId(): array
    {
        return self::VALID_HOTEL_ID;
    }
}