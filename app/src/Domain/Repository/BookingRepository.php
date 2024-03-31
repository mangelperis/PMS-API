<?php
declare(strict_types=1);


namespace App\Domain\Repository;

use App\Domain\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class BookingRepository extends ServiceEntityRepository implements BookingRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
        $this->entityManager = $this->getEntityManager();

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