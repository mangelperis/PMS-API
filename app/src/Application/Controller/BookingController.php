<?php
declare(strict_types=1);


namespace App\Application\Controller;

use App\Application\Service\BookingService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class BookingController extends AbstractFOSRestController
{
    const ROOM_PARAM_NAME = 'room';
    const HOTEL_PARAM_NAME = 'hotel';
    public function __construct(private BookingService $bookingService)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/call', name: 'call')]

    public function call(Request $request)
    {
        $roomNumber = $request->query->get(self::ROOM_PARAM_NAME);
        $hotelId = $request->query->get(self::HOTEL_PARAM_NAME);

        return $this->bookingService->run($hotelId, $roomNumber);
    }

}