<?php
declare(strict_types=1);


namespace App\Infrastructure\Mapper;

use App\Domain\Entity\Booking;
use App\Domain\Entity\Guest;
use App\Infrastructure\Adapter\PMSBookingDTO;
use App\Infrastructure\Serializer\CustomBookingDenormalizer;
use App\Infrastructure\Serializer\CustomBookingNormalizer;
use App\Infrastructure\Serializer\SerializerConstants;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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

    /** Uses CustomBookingDenormalizer, DTO to Entity
     * @param object $dtoObject
     * @return Booking
     * @throws \Exception
     */
    public function denormalizeToBooking(object $dtoObject): Booking
    {
        /** @var PMSBookingDTO $dtoObject */
        $booking = $dtoObject->getBooking();

        //Create collection<Guest>
        $guests = new ArrayCollection();
        $guestData = (object)$dtoObject->getGuest();
        $guest = new Guest(
            $guestData->name,
            $guestData->lastname,
            new DateTime($guestData->birthdate),
            $guestData->passport,
            $guestData->country,
        );
        $guests->add($guest);


        $newBooking = new Booking(
            $dtoObject->getHotelId(),
            $booking['locator'],
            $booking['room'],
            new DateTime($booking['check_in']),
            new DateTime($booking['check_out']),
            $guests,
        );

        $newBooking->setCreated($dtoObject->getCreated());
        return $newBooking;
    }

    /** Uses CustomBookingNormalizer, Entity to JSON filtering the defined groups in serializer/entity/Booking.yaml
     * @param Booking $booking
     * @return string
     */
    public function normalizeBookingToJSON(Booking $booking): string
    {
        return $this->serializer->serialize($booking, 'json', ['groups' => SerializerConstants::SERIALIZER_GROUP]);
    }

}