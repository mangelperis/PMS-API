<?php
declare(strict_types=1);


namespace App\Application\Controller;

use App\Application\Service\BookingService;
use App\Application\Service\Handler\ResponseHandler;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class BookingController extends AbstractFOSRestController
{
    const ROOM_PARAM_NAME = 'room';
    const HOTEL_PARAM_NAME = 'hotel';
    public function __construct(private readonly BookingService $bookingService, private ResponseHandler $responseHandler)
    {

    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/booking', name: 'get_booking_by_room_and_hotel', methods: ['GET'])]

    public function call(Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $roomNumber = $request->query->get(self::ROOM_PARAM_NAME);
        $hotelId = $request->query->get(self::HOTEL_PARAM_NAME);

        if(null === $roomNumber || null === $hotelId){
            return $this->responseHandler->createErrorResponse('Required parameter is missing', Response::HTTP_BAD_REQUEST);
        }

        return $this->bookingService->run($hotelId, $roomNumber);
    }

}