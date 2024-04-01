<?php
declare(strict_types=1);


namespace App\Infrastructure\Serializer;

use App\Domain\Entity\Booking;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CustomBookingDenormalizer implements DenormalizerInterface
{

    /**
     * Create a Booking entity from a BookingDTO.
     *
     * @param $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return Booking
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): Booking
    {
        //Validator after this call somewhere
        $dtoObject = (object)$data;

        /** @var object $data */
        return new Booking(
            $dtoObject->hotel_id,
            $dtoObject->booking->locator,
            $dtoObject->booking->room,
            $dtoObject->booking->check_in,
            $dtoObject->booking->check_out,
            $dtoObject->guest
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Booking::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        // Return the supported types for normalization
        return [Booking::class];
    }
}
