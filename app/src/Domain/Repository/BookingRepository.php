<?php
declare(strict_types=1);


namespace App\Domain\Repository;

use App\Domain\Entity\Booking;
use Doctrine\ORM\EntityRepository;
use Exception;

class BookingRepository extends EntityRepository implements BookingRepositoryInterface
{
    /**
     * @param Booking $booking
     * @return bool
     */
    public function save(Booking $booking): bool
    {
        try {
            $this->getEntityManager()->persist($booking);
            $this->getEntityManager()->flush();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}