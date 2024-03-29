<?php

namespace App\Tests\Unit\Domain;

use App\Domain\Entity\Booking;
use DateTime;
use PHPUnit\Framework\TestCase;

class BookingTest extends TestCase
{
    private object $sampleBookingObject;

    public function setUp(): void
    {
        $this->sampleBookingObject = (object)[
            'bookingId' => 'f8273b3c-9b69-4993-885f-2cb00687174a',
            'hotelId' => '70ce8358-600a-4bad-8ee6-acf46e1fb8db',
            'locator' => '649576941E9C7',
            'room' => '299',
            'checkIn' => '2023-06-23',
            'checkOut' => '2023-06-30',
            'guests' => [
                (object)[
                    'name' => 'Jesús',
                    'lastname' => 'Delagarza',
                    'birthdate' => '1974-08-05',
                    'passport' => 'MF-1645022-OZ',
                    'country' => 'MF',
                ],
            ],
        ];
    }

    public function testConstructorAndGetters()
    {
        // Sample data
        $bookingId = 'f8273b3c-9b69-4993-885f-2cb00687174a';
        $hotelId = '70ce8358-600a-4bad-8ee6-acf46e1fb8db';
        $locator = '649576941E9C7';
        $room = '299';
        $checkIn = new DateTime('2023-06-23');
        $checkOut = new DateTime('2023-06-30');
        $guests = [];

        $booking = new Booking(
            $bookingId,
            $hotelId,
            $locator,
            $room,
            $checkIn,
            $checkOut,
            $guests
        );

        // Assert that the constructor sets the properties correctly
        $this->assertSame($bookingId, $booking->getBookingId());
        $this->assertSame($hotelId, $booking->getHotelId());
        $this->assertSame($locator, $booking->getLocator());
        $this->assertSame($room, $booking->getRoom());
        $this->assertEquals($checkIn, $booking->getCheckIn());
        $this->assertEquals($checkOut, $booking->getCheckOut());
        $this->assertSame($guests, $booking->getGuests());
    }

    /**
     * @throws \Exception
     */
    public function testConstructorFromObject()
    {

        $booking = Booking::createFromObject($this->sampleBookingObject);

        // Assert that the method sets the properties correctly
        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertSame($this->sampleBookingObject->bookingId, $booking->getBookingId());
        $this->assertSame($this->sampleBookingObject->hotelId, $booking->getHotelId());
        $this->assertSame($this->sampleBookingObject->locator, $booking->getLocator());
        $this->assertSame($this->sampleBookingObject->room, $booking->getRoom());
        $this->assertEquals(new DateTime($this->sampleBookingObject->checkIn), $booking->getCheckIn());
        $this->assertEquals(new DateTime($this->sampleBookingObject->checkOut), $booking->getCheckOut());
        $this->assertCount(1, $booking->getGuests());

    }
}
