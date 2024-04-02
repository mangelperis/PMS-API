<?php
declare(strict_types=1);

namespace App\Tests\Integration;

use App\Domain\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BookingIntegrationTest extends WebTestCase
{

    const HOTEL_ID = '70ce8358-600a-4bad-8ee6-acf46e1fb8db';
    const ROOM = '298';

    //HTTP INTEGRATION
    public function testBookingIntegration(): void
    {
        $url = sprintf("/api/booking?hotel=%s&room=%s", self::HOTEL_ID, self::ROOM);

        $client = static::createClient();

        // Exec Http Call
        $client->request('GET', $url);

        // No errors
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Response is JSON
        $this->assertJson($client->getResponse()->getContent());

        // Verify that the requested booking exists in Database after all the service operations
        // To work it has to use a REAL hotelId and room
        $container = $client->getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $bookingRepository = $entityManager->getRepository(Booking::class);
        $booking = $bookingRepository->findOneBy(['hotelId' => self::HOTEL_ID, 'room' => self::ROOM]);
        $this->assertNotNull($booking);
    }

    //UNIT VALID PARAMS
    public function testCallWithValidParameters(): void
    {
        $client = static::createClient();
        $httpClient = $this->createMockHttpClient();
        $client->getContainer()->set(HttpClientInterface::class, $httpClient);

        $client->request('GET', '/api/booking?room=123&hotel=456');

        $this->assertResponseIsSuccessful();
    }

    //UNIT INVALID PARAMS
    public function testCallWithMissingParameters(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/booking');

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    private function createMockHttpClient(): MockHttpClient
    {
        $mockResponse = <<<JSON
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
        JSON;

        $responses = [
            new MockResponse($mockResponse, ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']]),
        ];

        return new MockHttpClient($responses);
    }
}