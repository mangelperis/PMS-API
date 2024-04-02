<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Application\Service\BookingService;
use App\Application\Service\Handler\ResponseHandler;
use App\Domain\Entity\Booking;
use App\Domain\Entity\Guest;
use App\Domain\Repository\BookingRepository;
use App\Domain\Service\PMStransformer;
use App\Infrastructure\Mapper\BookingMapper;
use App\Infrastructure\Service\PMSApiFetch;
use App\Infrastructure\Service\RedisCacheRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BookingServiceTest extends TestCase
{
    private PMSApiFetch $apiFetch;
    private PMStransformer $transformer;
    private RedisCacheRepository $cacheRepository;
    private LoggerInterface $logger;
    private ValidatorInterface $validator;
    private BookingMapper $bookingMapper;
    private BookingRepository $repository;
    private ResponseHandler $responseHandler;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->cacheRepository = $this->createMock(RedisCacheRepository::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->apiFetch = $this->createMock(PMSApiFetch::class);
        $this->transformer = $this->createMock(PMStransformer::class);
        $this->bookingMapper = $this->createMock(BookingMapper::class);
        $this->repository = $this->createMock(BookingRepository::class);
        $this->responseHandler = $this->createMock(ResponseHandler::class);

        $this->bookingService = new BookingService(
            $this->logger,
            $this->cacheRepository,
            $this->validator,
            $this->apiFetch,
            $this->transformer,
            $this->bookingMapper,
            $this->repository,
            $this->responseHandler
        );
    }

    /** NOT 100% TRUSTY!
     * @throws TransportExceptionInterface
     */
    public function testRun(): void
    {
        // Sample data + real entities
        $hotelId = '70ce8358-600a-4bad-8ee6-acf46e1fb8db';
        $locator = '65FD55D5021F3';
        $room = '298';
        $checkIn = new DateTime('2024-03-22');
        $checkOut = new DateTime('2024-04-02');
        $guests = new ArrayCollection();

        $booking = new Booking(
            $hotelId,
            $locator,
            $room,
            $checkIn,
            $checkOut
        );

        $guest = new Guest(
            'Leo',
            'Font',
            new DateTime('1972-03-31'),
            'CG-1089219-VP',
            'CG',
        );

        $guest->setBooking($booking);
        $guests->add($guest);
        $booking->addGuest($guest);

        $jsonResponse = <<<JSON
            {
                "bookingId": "2f648634-25d9-41e5-bd3c-12c4ae5ccf42",
                "hotel": "70ce8358-600a-4bad-8ee6-acf46e1fb8db",
                "locator": "65FD55D5021F3",
                "room": "298",
                "checkIn": "2024-03-22",
                "checkOut": "2024-04-02",
                "numberOfNights": 11,
                "totalPax": 1,
                "guests": [
                    {
                        "name": "Leo",
                        "lastname": "Font",
                        "birthdate": "1972-03-31",
                        "passport": "CG-1089219-VP",
                        "country": "CG",
                        "age": 52
                    }
                ]
            }          
        JSON;

        $jsonSource = <<<JSON
           {"bookings" : [
                {
                  "hotel_id": "49001",
                  "hotel_name": "Hotel con ID Externo 49001",
                  "guest": {
                    "name": "Juan",
                    "lastname": "Madrigal",
                    "birthdate": "1999-12-06",
                    "passport": "WF-1495889-GR",
                    "country": "ES"
                  },
                  "booking": {
                    "locator": "61F80321790C5",
                    "room": "291",
                    "check_in": "2022-01-31",
                    "check_out": "2022-02-08",
                    "pax": {
                      "adults": 1,
                      "kids": 0,
                      "babies": 0
                    }
                  },
                  "created": "2022-01-31 17:39:38",
                  "signature": "e8b558125c709621bd5a80ca25f772cc7a3a4b8b0b86478f355740af5d7558a8"
                }
          ],
          "total": 1
          }
        JSON;
//-------------------------------------------------------------------//

        // Redis timestamp call get (no data)
        $this->cacheRepository->expects($this->once())
            ->method('getLastCreatedTimestamp')
            ->willReturn(0);

        // Response from PMS API
        $response = $this->createStub(ResponseInterface::class);
        $this->apiFetch->expects($this->once())
            ->method('__invoke')
            ->with(0)
            ->willReturn($response);

        //Set Response
        $response->method('getContent')->willReturn($jsonSource);

        // Transformed booking data
        $transformedData = [$booking];
        $this->transformer->expects($this->once())
            ->method('__invoke')
            ->willReturn(['DTO' => $transformedData, 'errors' => []]);

        // Persist Booking will depend on if the test database is empty!
        $this->repository->expects($this->any())
            ->method('save')
            ->willReturn(true);

        // Redis timestamp call set will depend on if there are persists actions
        $this->cacheRepository->expects($this->any())
            ->method('setLastCreatedTimestamp');

        // Database fetch
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['hotelId' => $hotelId, 'room' => $room])
            ->willReturn($booking);

        // JsonResponse handler
        $this->responseHandler->expects($this->once())
            ->method('createResponse')
            ->willReturn(new JsonResponse());

        // Exec run
        $response = $this->bookingService->run($hotelId, $room);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

}

