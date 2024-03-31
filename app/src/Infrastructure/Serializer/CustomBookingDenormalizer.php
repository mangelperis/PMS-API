<?php
declare(strict_types=1);


namespace App\Infrastructure\Serializer;

use App\Domain\Entity\Booking;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CustomBookingDenormalizer implements DenormalizerInterface
{

    private array $requiredPMSKeys;

    public function __construct(array $keys = null)
    {
        $this->requiredPMSKeys = $keys ?? SerializerConstants::PMS_REQUIRED_KEYS;
    }

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

        //Filter to the keys required
        /** @var array $data */
        $filterData = $this->filterData($data);

        return new Booking(
            $filterData->hotel_id,
            $filterData->booking->locator,
            $filterData->booking->room,
            $filterData->booking->check_in,
            $filterData->booking->check_out,
            $filterData->guest
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === Booking::class;
    }


    /**
     * Function to filter out only the keys that you need
     * @param array $input
     * @return object
     */
    private function filterData(array $input): object
    {
        $filteredData = array_intersect_key($input, array_flip($this->requiredPMSKeys));
        return (object)$filteredData;
    }

}
