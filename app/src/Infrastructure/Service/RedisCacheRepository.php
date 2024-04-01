<?php
declare(strict_types=1);


namespace App\Infrastructure\Service;


use App\Domain\Entity\Booking;
use Predis\Client;

class RedisCacheRepository
{
    const KEY_LAST_TIMESTAMP = 'pms:booking:created';
    private Client $redis;

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * @return int|null
     */
    public function getLastCreatedTimestamp(): ?int
    {
        $value = $this->redis->get(self::KEY_LAST_TIMESTAMP);

        if(null === $value){
            return (int) 0;
        }

        return (int) $value;
    }

    /**
     * @param int $timestamp
     * @return void
     */
    public function setLastCreatedTimestamp(int $timestamp): void
    {
        $this->redis->set(self::KEY_LAST_TIMESTAMP, $timestamp);
    }

    /**
     * @param Booking $entity
     * @param string $json
     * @return void
     */
    public function storeEntity(Booking $entity, string $json): void
    {
        $key = $this->generateKey($entity->getHotelId(), $entity->getRoom());
        $this->redis->set($key, $json);
    }

    /**
     * @param string $hotelId
     * @param string $room
     * @return Booking|null
     */
    public function readEntity(string $hotelId, string $room): ?string
    {
        $key = $this->generateKey($hotelId, $room);
        $serializedEntity = $this->redis->get($key);
        if (!$serializedEntity) {
            return null;
        }
        // Delete the entity from Redis
        $this->redis->del($key);
        return $serializedEntity;
    }

    /**
     * @param string $hotelId
     * @param string $room
     * @return string
     */
    private function generateKey(string $hotelId, string $room): string
    {
        return "entity:{$hotelId}:{$room}";
    }

}