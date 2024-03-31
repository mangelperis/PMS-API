<?php
declare(strict_types=1);


namespace App\Infrastructure\Service;


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

}