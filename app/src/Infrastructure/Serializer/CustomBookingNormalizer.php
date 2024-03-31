<?php
declare(strict_types=1);


namespace App\Infrastructure\Serializer;

use App\Domain\Entity\Booking;
use App\Domain\Entity\Guest;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CustomBookingNormalizer implements NormalizerInterface
{
    private CustomGuestNormalizer $guestNormalizer;

    public function __construct(CustomGuestNormalizer $guestNormalizer)
    {
        $this->guestNormalizer = $guestNormalizer;
    }


    /**
     * @param $object
     * @param string|null $format
     * @param array $context
     * @return array
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Booking $booking */
        $booking = clone $object;
        $guests = $booking->getGuests();
        $normalizedGuests = [];

        // Normalized guests instead of manual array_map
        /** @var Guest $guest */
        foreach ($guests as $guest) {
            $normalizedGuests[] = $this->guestNormalizer->normalize($guest, 'array', [SerializerConstants::SERIALIZER_GROUP]);
        }

        return [
            'bookingId' => $booking->getBookingId(),
            'hotel' => $booking->getHotelId(),
            'locator' => $booking->getLocator(),
            'room' => $booking->getRoom(),
            'checkIn' => $booking->getCheckIn()->format('Y-m-d'),
            'checkOut' => $booking->getCheckOut()->format('Y-m-d'),
            'numberOfNights' => $this->getNumberOfNights($booking),
            'totalPax' => count($booking->getGuests()),
            'guests' => $normalizedGuests,
        ];
    }

    /**
     * Get the number of nights for the booking.
     *
     * @param Booking $booking
     * @return int
     */
    public function getNumberOfNights(Booking $booking): int
    {
        $checkIn = $booking->getCheckIn();
        $checkOut = $booking->getCheckOut();

        // Calculate the interval between checkIn and checkOut dates
        $interval = $checkOut->diff($checkIn);

        // Return the number of nights (days)
        return $interval->days;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Booking;
    }
}