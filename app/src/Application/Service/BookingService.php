<?php
declare(strict_types=1);


namespace App\Application\Service;

use App\Domain\Entity\Booking;
use App\Domain\Service\PMStransformer;
use App\Infrastructure\Adapter\PMSBookingDTO;
use App\Infrastructure\Mapper\BookingMapper;
use App\Infrastructure\Service\PMSApiFetch;
use App\Infrastructure\Service\RedisCacheRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BookingService
{
    private PMSApiFetch $apiFetch;
    private PMStransformer $transformer;
    private RedisCacheRepository $cacheRepository;
    private LoggerInterface $logger;
    private BookingMapper $bookingMapper;


    public function __construct(
        LoggerInterface      $logger,
        RedisCacheRepository $cacheRepository,
        PMSApiFetch          $apiFetch,
        PMStransformer       $transformer,
        BookingMapper        $bookingMapper
    )
    {
        $this->logger = $logger;
        $this->cacheRepository = $cacheRepository;
        $this->apiFetch = $apiFetch;
        $this->transformer = $transformer;
        $this->bookingMapper = $bookingMapper;
    }


    /**
     * @param string $hotelId
     * @param string $roomNumber
     * @return void
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function run(string $hotelId, string $roomNumber): void
    {
        //ETL process
        $response = $this->fetchBookingData($hotelId, $roomNumber);
        $dataSource = $this->processBookingResponse($response);
        $transformed = $this->transformBookingData($dataSource);
        //Persist data, normalize



    }

    /**
     * @param string $hotelId
     * @param string $roomNumber
     * @return array|null
     * @throws Exception|TransportExceptionInterface
     */
    private function fetchBookingData(string $hotelId, string $roomNumber): ?ResponseInterface
    {
        try {
            //Using cache system as trigger, the very first time the KEY won't exist in Redis (null)
            //so no timestamp will be available in cache, and it will send the param '0'
            //otherwise it will attach the last created booking timestamp
            $timestamp = $this->cacheRepository->getLastCreatedTimestamp();

            // Fetch data from the API (PMS)
            return $this->pmsApiFetch($timestamp);
        } catch (Exception $e) {
            // Handle API request errors
            $this->logger->error(sprintf("API fetch fail: %s", $e->getMessage()));
            throw new Exception('Error while fetching data from source');
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function pmsApiFetch(int $timestamp): ResponseInterface
    {
        return ($this->apiFetch)($timestamp);
    }

    /**
     * @param ResponseInterface $response
     * @return array|null
     * @throws Exception
     */
    private function processBookingResponse(ResponseInterface $response): ?array
    {
        try {
            $content = $response->getContent();
            $data = json_decode($content, true);

            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(sprintf('Error decoding JSON: %s', json_last_error_msg()));
            }

            return $data;

        } catch (ClientExceptionInterface|TransportExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            $this->logger->error(sprintf("API process response fail: %s", $e->getMessage()));
            throw new Exception('Error while processing source response');

        }
    }


    /**
     * @param array $data
     * @return array|null
     * @throws Exception
     */
    private function transformBookingData(array $data): ?array
    {
        try {
            if (null !== $data['total']) {
                $this->logger->info(sprintf("Transforming [%d] bookings", $data['total']));
            }

            /** @var array<Booking> $bookings */
            $bookings = [];
            $transformer = ($this->transformer)($data['bookings']);

            $errors = $transformer['errors'];

            /** @var string $error */
            foreach ($errors as $error) {
                $this->logger->warning(sprintf("Transformer error: %s", $error));
            }

            $bookingsDTO = $transformer['DTO'];

            /** @var PMSBookingDTO $dto */
            foreach ($bookingsDTO as $dto) {
                $bookings[] = $this->bookingMapper->denormalizeToBooking($dto);
            }
            $lastTimestamp = $transformer['lastTimestamp'];

            return $bookings;

        } catch (Exception $e) {
            $this->logger->error(sprintf("API transform data fail: %s", $e->getMessage()));
            throw new Exception('Error while transforming data');
        }

    }

    private function returnBookingData()
    {

    }
}