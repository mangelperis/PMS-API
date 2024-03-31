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


    /**
     * @throws Exception
     */
    public function __invoke(array $data): array
    {
        $arrayDTO = [];
        $errors = [];
        $lastTimestamp = 0;

        foreach ($data as $booking) {
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
            $object = (object)($data);

            //Create DTO object
            $arrayDTO[] = new PMSBookingDTO(
                $object->hotel_id,
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


}