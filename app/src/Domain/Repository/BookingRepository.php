<?php
declare(strict_types=1);


namespace App\Domain\Repository;

use App\Domain\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;

class BookingRepository extends EntityRepository implements BookingRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Booking::class));
        $this->entityManager = $entityManager;
    }

    /**
     * @param Booking $booking
     * @return bool
     */
    public function save(Booking $booking): bool
    {
        try {
            $this->entityManager->persist($booking);
            $this->entityManager->flush();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}