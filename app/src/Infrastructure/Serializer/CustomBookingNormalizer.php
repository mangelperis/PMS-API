<?php
declare(strict_types=1);


namespace App\Infrastructure\Serializer;

use App\Domain\Entity\Booking;
use App\Domain\Entity\Guest;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CustomBookingNormalizer  implements NormalizerInterface
{
    /**
     * @param $object
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Booking $booking */
        $booking = clone $object;
        return [
            'bookingId' => $booking->getBookingId(),
            'hotel' => $booking->getHotelId(),
            'locator' => $booking->getLocator(),
            'room' => $booking->getRoom(),
            'checkIn' => $booking->getCheckIn()->format('Y-m-d'),
            'checkOut' => $booking->getCheckOut()->format('Y-m-d'),
            'numberOfNights' => $this->getNumberOfNights($booking),
            'totalPax' => count($booking->getGuests()),
            'guests' => array_map(function (Guest $guest) {
                return [
                    'name' => $guest->getName(),
                    'lastname' => $guest->getLastname(),
                    'birthdate' => $guest->getBirthdate()->format('Y-m-d'),
                    'passport' => $guest->getPassport(),
                    'country' => $guest->getCountry(),
                ];
            }, $booking->getGuests()->toArray()),
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