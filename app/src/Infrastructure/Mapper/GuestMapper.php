<?php
declare(strict_types=1);


namespace App\Infrastructure\Mapper;

use App\Domain\Entity\Guest;
use App\Infrastructure\Serializer\SerializerConstants;
use Symfony\Component\Serializer\SerializerInterface;

class GuestMapper
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /** Uses CustomGuestDenormalizer, Array to Entity
     * @param array $array
     * @return Guest
     */
    public function denormalizeToGuest(array $array): Guest
    {
        return $this->serializer->deserialize($array, Guest::class, 'array');
    }

    /** Uses CustomGuestNormalizer, Entity to JSON filtering the defined groups in serializer/entity/Guest.yaml
     * @param Guest $guest
     * @return mixed
     */
    public function normalizeBookingToJSON(Guest $guest): string
    {
        return $this->serializer->serialize($guest, 'json', ['groups' => SerializerConstants::SERIALIZER_GROUP]);
    }
}