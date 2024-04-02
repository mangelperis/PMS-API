<?php
declare(strict_types=1);

namespace App\Tests\Infrastructure\Service;

use App\Domain\Entity\Booking;
use App\Infrastructure\Service\RedisCacheRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisCacheRepositoryTest extends TestCase
{
    const REDIS_HOST = 'redis';
    const REDIS_TIMESTAMP_KEY = RedisCacheRepository::KEY_LAST_TIMESTAMP;

    private RedisCacheRepository $redisCacheRepository;
    private Client $redisClient;

    protected function setUp(): void
    {
        // IMPORTANT!! -> set host / port accordingly
        $this->redisClient = new Client(['host' => self::REDIS_HOST]);
        $this->redisCacheRepository = new RedisCacheRepository($this->redisClient);
    }

    public function testGetLastCreatedTimestamp(): void
    {
        $this->redisClient->set(self::REDIS_TIMESTAMP_KEY, 123456);

        $timestamp = $this->redisCacheRepository->getLastCreatedTimestamp();

        $this->assertEquals(123456, $timestamp);
    }

    public function testSetLastCreatedTimestamp(): void
    {
        $this->redisCacheRepository->setLastCreatedTimestamp(5);

        // Assert that the value was set correctly in Redis
        $this->assertEquals('5', $this->redisClient->get(self::REDIS_TIMESTAMP_KEY));
    }

    public function testStoreEntity(): array
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

        $json = '{"example": "json"}';
        $key = "entity:{$hotelId}:{$room}";


        // Store the entity in Redis
        $this->redisCacheRepository->storeEntity($booking, $json);

        // Retrieve the stored JSON from Redis
        $storedJson = $this->redisClient->get($key);

        // Assert that the stored JSON matches the expected value
        $this->assertEquals($json, $storedJson);

        //Will reuse to read
        return [
           'hotel' => $hotelId,
           'room' => $room,
           'json' => $json,
        ];
    }

    public function testReadEntity(): void
    {
        //Previous test data (SET)
        $storedEntity = $this->testStoreEntity();

        $storedJson = $this->redisCacheRepository->readEntity($storedEntity['hotel'], $storedEntity['room']);

        $this->assertEquals($storedEntity['json'], $storedJson);

        //key is deleted after reading
        $this->assertNull($this->redisClient->get('entity:hotel:room'));
    }
}

