<?php

namespace App\Tests\Unit\Domain;

use App\Domain\Entity\Booking;
use App\Domain\Entity\Guest;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BookingTest extends TestCase
{
    private object $sampleBookingObject;

    public function setUp(): void
    {
        $this->sampleBookingObject = (object)[
            'hotelId' => '70ce8358-600a-4bad-8ee6-acf46e1fb8db',
            'locator' => '649576941E9C7',
            'room' => '299',
            'checkIn' => '2023-06-23',
            'checkOut' => '2023-06-30',
            'guests' => new ArrayCollection([
                (object)[
                    'name' => 'Jesús',
                    'lastname' => 'Delagarza',
                    'birthdate' => '1974-08-05',
                    'passport' => 'MF-1645022-OZ',
                    'country' => 'MF',
                ],
            ]),
        ];
    }

    public function testConstructorAndGetters()
    {
        // Sample data
        $hotelId = '70ce8358-600a-4bad-8ee6-acf46e1fb8db';
        $locator = '649576941E9C7';
        $room = '299';
        $checkIn = new DateTime('2023-06-23');
        $checkOut = new DateTime('2023-06-30');
        $guests = new ArrayCollection();

        $booking = new Booking(
            $hotelId,
            $locator,
            $room,
            $checkIn,
            $checkOut
        );

        $guest = new Guest(
            'Paco',
            'Jons',
            new DateTime('1990-06-17'),
            'M-1359285-EU',
            'FR',
        );

        $guest->setBooking($booking);
        $guests->add($guest);
        $booking->addGuest($guest);

        //dd($guests, $booking->getGuests());

        // Assert that the constructor sets the properties correctly
        $this->assertSame($hotelId, $booking->getHotelId());
        $this->assertSame($locator, $booking->getLocator());
        $this->assertSame($room, $booking->getRoom());
        $this->assertEquals($checkIn, $booking->getCheckIn());
        $this->assertEquals($checkOut, $booking->getCheckOut());
        $this->assertEquals($guests, $booking->getGuests());
    }

}
