<?php

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Entity\Booking;
use App\Infrastructure\Adapter\BookingRepositoryDoctrineAdapter;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;


class BookingRepositoryTest extends TestCase
{
    private BookingRepositoryDoctrineAdapter $bookingRepository;
    private EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with(Booking::class)
            ->willReturn($classMetadata);

        $this->bookingRepository = new BookingRepositoryDoctrineAdapter($this->entityManager);
    }

    public function testSave(): void
    {
        // Sample data
        $hotelId = '70ce8358-600a-4bad-8ee6-acf46e1fb8db';
        $locator = '649576941E9C7';
        $room = '299';
        $checkIn = new DateTime('2023-06-23');
        $checkOut = new DateTime('2023-06-30');

        $booking = new Booking(
            $hotelId,
            $locator,
            $room,
            $checkIn,
            $checkOut
        );

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($booking);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->bookingRepository->save($booking);
        $this->assertTrue($result);
    }
}
