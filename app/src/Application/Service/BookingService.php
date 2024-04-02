<?php
declare(strict_types=1);


namespace App\Application\Service;

use App\Application\Service\Handler\ResponseHandler;
use App\Domain\Entity\Booking;
use App\Domain\Repository\BookingRepository;
use App\Domain\Service\PMStransformer;
use App\Infrastructure\Adapter\PMSBookingDTO;
use App\Infrastructure\Mapper\BookingMapper;
use App\Infrastructure\Service\PMSApiFetch;
use App\Infrastructure\Service\RedisCacheRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
    private ValidatorInterface $validator;
    private BookingMapper $bookingMapper;
    private BookingRepository $repository;
    private ResponseHandler $responseHandler;


    public function __construct(
        LoggerInterface      $logger,
        RedisCacheRepository $cacheRepository,
        ValidatorInterface   $validator,
        PMSApiFetch          $apiFetch,
        PMStransformer       $transformer,
        BookingMapper        $bookingMapper,
        BookingRepository    $repository,
        ResponseHandler      $responseHandler,
    )
    {
        $this->logger = $logger;
        $this->cacheRepository = $cacheRepository;
        $this->validator = $validator;
        $this->apiFetch = $apiFetch;
        $this->transformer = $transformer;
        $this->bookingMapper = $bookingMapper;
        $this->repository = $repository;
        $this->responseHandler = $responseHandler;
    }


    /**
     * @param string $hotelId
     * @param string $roomNumber
     * @return JsonResponse
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function run(string $hotelId, string $roomNumber): JsonResponse
    {
        //ETL process
        $response = $this->fetchBookingData();
        $dataSource = $this->processBookingResponse($response);
        $transformed = $this->transformBookingData($dataSource);
        //Attempt to Persist only when there's new content
        if (null !== $transformed) {
            $persist = $this->persistBookingData($transformed, $hotelId, $roomNumber);
        }
        //Query Results always executed
        return $this->returnBookingData($hotelId, $roomNumber);
    }

    /**
     * @return array|null
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    private function fetchBookingData(): ?ResponseInterface
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
            $content = $response->getContent();//String (json)

            $data = json_decode($content, true);//Array

            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(sprintf('Error decoding JSON: %s', json_last_error_msg()));
            }

            /** @var array $data */
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
            //Empty Source, no empty rooms, all sold!
            if (!isset($data['total']) && !isset($data['bookings'])) {
                $this->logger->info("No empty rooms, all sold!");
                return null;
            }

            if (null !== $data['total']) {
                $this->logger->info(sprintf("Transforming [%d] bookings", $data['total']));
            }

            //Transform source Data to DTO
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

            return $bookings;

        } catch (Exception $e) {
            $this->logger->error(sprintf("API transform data fail: %s", $e->getMessage()));
            throw new Exception('Error while transforming data');
        }

    }

    /**
     * @param array $data
     * @param string $hotelId
     * @param string $roomNumber
     * @return bool
     */
    private function persistBookingData(array $data, string $hotelId, string $roomNumber): bool
    {
        try {
            $timestamp = 0;
            foreach ($data as $booking) {
                /** @var Booking $booking */
                //Validate Entity Asserts
                if (!$this->validator->validate($booking)) {
                    $this->logger->notice(sprintf("Invalid Booking [%s]-[%s]", $booking->getLocator(), $booking->getHotelId()));
                    continue;
                }
                //Persist if it doesn't exist "ON duplicate UPDATE" would be ideal
                /** @var Booking $existingBooking */
                $existingBooking = $this->repository->findOneBy(['hotelId' => $booking->getHotelId(), 'room' => $booking->getRoom()]);

                // Already exists skip
                if(null !== $existingBooking){
                    //Consider to update the Booking Details here...
                    $this->logger->notice(sprintf("Booking w/ [%s]-[%s] already exists", $booking->getHotelId(), $booking->getRoom()));
                    continue;
                }

                $result = $this->repository->save($booking);

                //When saved OK
                if ($result) {
                    $bookingTimestamp = $booking->getCreated()->getTimestamp();

                    //Sets last timestamp logic, preventing failure of DB on the next iteration will remind status
                    //"Incremental approach"
                    if ($bookingTimestamp > $timestamp) {
                        $timestamp = $bookingTimestamp;
                        $this->cacheRepository->setLastCreatedTimestamp($timestamp);
                    }

                    //Set on system cache the requested API data if matches, to prevent query the database again later
                    if ($booking->getHotelId() === $hotelId && $booking->getRoom() === $roomNumber) {
                        //need serialization to store the json
                        $json = $this->bookingMapper->normalizeBookingToJSON($booking);
                        $this->cacheRepository->storeEntity($booking, $json);
                    }
                }

            }

            return true;
        } catch (Exception $exception) {
            $this->logger->error(sprintf("API persist data fail: %s", $exception->getMessage()));
            return false;
        }

    }

    /**
     * @param string $hotelId
     * @param string $roomNumber
     * @return JsonResponse
     */
    private function returnBookingData(string $hotelId, string $roomNumber): JsonResponse
    {
        try {
            //Use cache to detect is the requested booking was persisted in this operation and return it
            $cachedBooking = $this->cacheRepository->readEntity($hotelId, $roomNumber);
            if (null !== $cachedBooking) {
                return $this->responseHandler->createResponse($cachedBooking);
            }

            //Find in DB logic
            $booking = $this->repository->findOneBy(['hotelId' => $hotelId, 'room' => $roomNumber]);

            if (null !== $booking) {
                $json = $this->bookingMapper->normalizeBookingToJSON($booking);
                return $this->responseHandler->createResponse($json);
            }

            return $this->responseHandler->createSuccessResponse([]);
        } catch (Exception $exception) {
            $this->logger->error(sprintf("API return data fail: %s", $exception->getMessage()));
            return $this->responseHandler->createErrorResponse($exception->getMessage());
        }
    }
}