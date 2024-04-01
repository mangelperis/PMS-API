<?php
declare(strict_types=1);


namespace App\Domain\Service;

use App\Infrastructure\Adapter\PMSBookingDTO;
use DateTime;
use Exception;

class PMStransformer
{
    //PMS Input keys to map
    public const PMS_REQUIRED_KEYS = ['hotel_id', 'booking', 'guest'];
    //Source ID to Destination ID
    public const HOTEL_ID_MAP = [
        '36001' => '70ce8358-600a-4bad-8ee6-acf46e1fb8db',
        '28001' => '3cbcd874-a7e0-4bb3-987e-eb36f05b7e7a',
        '28003' => 'ca385c3b-c2b1-4691-b433-c8cd51883d25',
        '49001' => '5ab1d247-19ea-4850-9242-2d3ffbbdb58d',
    ];

    /**
     * @throws Exception
     */
    public function __invoke(array $data): array
    {
        $arrayDTO = [];
        $errors = [];
        $lastTimestamp = 0;

        foreach ($data as $key => $booking) {
            $loggerBookingId = $booking['booking']['locator'];
            //Check `created` timestamp is there
            if (null === $booking['created']) {
                $errors[] = sprintf("Booking object -%s- missing `created`", $loggerBookingId);
                continue;
            }

            //Check REQUIRED keys are there
            if (!$this->validateKeys($booking)) {
                $errors[] = sprintf("Booking object -%s- missing `REQUIRED KEYS`", $loggerBookingId);
                continue;
            }

            //Use only the REQUIRED elements
            $object = (object)($booking);

            //Create DTO object
            $arrayDTO[] = new PMSBookingDTO(
                $this->transformHotelId($object->hotel_id),
                $object->hotel_name,
                $object->guest,
                $object->booking,
                new DateTime($object->created),
                $object->signature,
            );

            $timestamp = strtotime($booking['created']);

            if ($timestamp > $lastTimestamp) {
                $lastTimestamp = $timestamp;
            }
        }

        return [
            'DTO' => $arrayDTO,
            'errors' => $errors,
            'lastTimestamp' => $lastTimestamp
        ];
    }


    /**
     * @param array $data
     * @return bool
     */
    private function validateKeys(array $data): bool
    {
        foreach (self::PMS_REQUIRED_KEYS as $key) {
            if (!array_key_exists($key, $data)) {
                return false; // Return false if any required key is missing
            }
        }
        return true;
    }

    /** Transform if exists in expected, otherwise use source
     * @param string $hotelId
     * @return string
     */
    private function transformHotelId(string $hotelId): string
    {
        return self::HOTEL_ID_MAP[$hotelId] ?? $hotelId;
    }


}