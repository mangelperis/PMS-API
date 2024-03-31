<?php

namespace App\Tests\Unit\Repository;

use App\Domain\Entity\Booking;
use App\Domain\Repository\BookingRepository;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class BookingRepositoryTest extends TestCase
{
    private BookingRepository $bookingRepository;
    public function setUp():void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManagerForClass')->willReturn($entityManager);

        $this->bookingRepository = new BookingRepository($managerRegistry, $entityManager);
        $this->assertInstanceOf(BookingRepository::class, $this->bookingRepository);
    }

    public function testSave(): void
    {
        // Sample data
        $bookingId = 'f8273b3c-9b69-4993-885f-2cb00687174a';
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

        $result = $this->bookingRepository->save($booking);

        $this->assertTrue($result);
    }
}
