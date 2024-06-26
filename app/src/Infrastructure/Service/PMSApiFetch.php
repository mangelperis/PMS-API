<?php
declare(strict_types=1);


namespace App\Infrastructure\Service;

use DateTime;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PMSApiFetch
{
    const PMS_URL = "https://cluster-dev.stay-app.com/sta/pms-faker/stay/test/pms?ts=%d";

    public function __construct(private readonly HttpClientInterface $client)
    {
    }

    /**
     * @throws TransportExceptionInterface|Exception
     */
    public
    function __invoke(int $timestamp = 0): ResponseInterface
    {
        if (!$this->isValidTimestamp($timestamp)) {
            throw new Exception("Invalid Timestamp", Response::HTTP_BAD_REQUEST);
        }

        $url = sprintf(self::PMS_URL, $timestamp);

        return $this->client->request('GET', $url);
    }

    /**
     * @param int $timestamp
     * @return bool
     */
    public function isValidTimestamp(int $timestamp): bool
    {
        $dateTime = DateTime::createFromFormat('U', (string)$timestamp);
        return $dateTime && $dateTime->format('U') === (string)$timestamp;
    }


}