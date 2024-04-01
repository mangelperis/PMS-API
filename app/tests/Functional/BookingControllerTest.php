<?php

namespace App\Tests\Functional;

use App\Application\Controller\BookingController;
use App\Application\Service\BookingService;
use App\Application\Service\Handler\ResponseHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class BookingControllerTest extends TestCase
{
    private BookingService $service;
    private ResponseHandler $responseHandler;
    private BookingController $controller;

    public function setUp(): void
    {
        $this->service = $this->createStub(BookingService::class);
        $this->responseHandler = $this->createStub(ResponseHandler::class);
        $this->controller =  new BookingController($this->service, $this->responseHandler);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCallWithValidParameters(): void
    {

        $request = new Request([BookingController::ROOM_PARAM_NAME => '123', BookingController::HOTEL_PARAM_NAME => '456']);

        $this->service->expects($this->once())
            ->method('run')
            ->with('456', '123')
            ->willReturn(new JsonResponse());

        $response = $this->controller->call($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCallWithMissingParameters(): void
    {
        $request = new Request([BookingController::ROOM_PARAM_NAME => '123']);

        $this->responseHandler->expects($this->once())
            ->method('createErrorResponse')
            ->with('Required parameter is missing', Response::HTTP_BAD_REQUEST);

        $response = $this->controller->call($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
