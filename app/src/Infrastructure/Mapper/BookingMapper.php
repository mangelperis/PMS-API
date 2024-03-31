<?php
declare(strict_types=1);


namespace App\Infrastructure\Mapper;

use App\API\DTO\BookingDTO;
use App\Domain\Entity\Booking;
use App\Infrastructure\Serializer\CustomBookingDenormalizer;
use App\Infrastructure\Serializer\CustomBookingNormalizer;
use App\Infrastructure\Serializer\SerializerConstants;
use Symfony\Component\Serializer\SerializerInterface;

class BookingMapper
{
    private SerializerInterface $serializer;
    private CustomBookingDenormalizer $bookingDenormalizer;
    private CustomBookingNormalizer $bookingNormalizer;

    public function __construct(
        SerializerInterface $serializer,

    )
    {
        $this->serializer = $serializer;

    }

    /** Uses CustomBookingDenormalizer, Array to Entity
     * @param array $array
     * @return Booking
     */
    public function denormalizeToBooking(array $array): Booking
    {
        return $this->serializer->deserialize($array, Booking::class, 'array');
    }

    /** Uses CustomBookingNormalizer, Entity to JSON filtering the defined groups in serializer/entity/Booking.yaml
     * @param Booking $booking
     * @return mixed
     */
    public function normalizeBookingToJSON(Booking $booking): string
    {
        return $this->serializer->serialize($booking, 'json', ['groups' => SerializerConstants::SERIALIZER_GROUP]);
    }

}