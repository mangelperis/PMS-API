<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Application\Service\BookingService;
use App\Application\Service\Handler\ResponseHandler;
use App\Domain\Entity\Booking;
use App\Domain\Repository\BookingRepository;
use App\Domain\Service\PMStransformer;
use App\Infrastructure\Mapper\BookingMapper;
use App\Infrastructure\Service\PMSApiFetch;
use App\Infrastructure\Service\RedisCacheRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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

    public function testRun(): void
    {

    }

}

