<?php
declare(strict_types=1);


namespace App\Infrastructure\Mapper;

use App\API\DTO\BookingDTO;
use App\Domain\Entity\Booking;
use Symfony\Component\Serializer\SerializerInterface;

class BookingMapper
{
    const SERIALIZER_GROUP = 'STAY';
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Booking $booking
     * @return BookingDTO
     */
    public function mapToDTO(Booking $booking): BookingDTO
    {
        $dtoData = $this->serializer->normalize($booking, null, ['groups' => self::SERIALIZER_GROUP]);

        return $this->serializer->denormalize($dtoData, BookingDTO::class);
    }
}