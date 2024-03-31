<?php

namespace App\Tests\Unit\Repository;

use App\Domain\Entity\Booking;
use App\Domain\Repository\BookingRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;


class BookingRepositoryTest extends TestCase
{
    private BookingRepository $bookingRepository;
    private EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->bookingRepository = new BookingRepository($this->entityManager, new ClassMetadata(Booking::class));
    }

    public function testSave(): void
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
            $checkOut,
            $guests
        );

        $this->entityManager->expects($this->once())->method('persist')->with($booking);
        $this->entityManager->expects($this->once())->method('flush');

        $result = $this->bookingRepository->save($booking);
        $this->assertTrue($result);

        $this->entityManager->expects($this->once())->method('find')->willReturn($booking);

        $savedBooking = $this->bookingRepository->find($booking->getBookingId());
        $this->assertInstanceOf(Booking::class, $savedBooking);

        $this->assertEquals($hotelId, $savedBooking->getHotelId());
        $this->assertEquals($locator, $savedBooking->getLocator());
        $this->assertEquals($room, $savedBooking->getRoom());
        $this->assertEquals($checkIn, $savedBooking->getCheckIn());
        $this->assertEquals($checkOut, $savedBooking->getCheckOut());
    }
}
