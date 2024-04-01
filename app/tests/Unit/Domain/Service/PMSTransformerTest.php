<?php

namespace App\Tests\Unit\Domain\Service;

use App\Domain\Service\PMStransformer;
use App\Infrastructure\Adapter\PMSBookingDTO;
use DateTime;
use PHPUnit\Framework\TestCase;

class PMSTransformerTest extends TestCase
{
    public const HOTEL_ID_MAP = [
        '36001' => '70ce8358-600a-4bad-8ee6-acf46e1fb8db',
        '28001' => '3cbcd874-a7e0-4bb3-987e-eb36f05b7e7a',
        '28003' => 'ca385c3b-c2b1-4691-b433-c8cd51883d25',
        '49001' => '5ab1d247-19ea-4850-9242-2d3ffbbdb58d',
    ];

    /**
     * @throws \Exception
     */
    public function testInvokeWithValidData(): void
    {
        $transformer = new PMStransformer();

        $data = [
            [
                'hotel_id' => '49001',
                'hotel_name' => 'Hotel con ID Externo 49001',
                'guest' => [
                    'name' => 'Juan',
                    'lastname' => 'Madrigal',
                    'birthdate' => '1999-12-06',
                    'passport' => 'WF-1495889-GR',
                    'country' => 'ES',
                ],
                'booking' => [
                    'locator' => '61F80321790C5',
                    'room' => '291',
                    'check_in' => '2022-01-31',
                    'check_out' => '2022-02-08',
                    'pax' => [
                        'adults' => 1,
                        'kids' => 0,
                        'babies' => 0,
                    ],
                ],
                'created' => '2022-01-31 17:39:38',
                'signature' => 'e8b558125c709621bd5a80ca25f772cc7a3a4b8b0b86478f355740af5d7558a8',
            ],
        ];

        $result = $transformer($data);
        $this->assertCount(count($data), $result['DTO']);

        foreach ($result['DTO'] as $index => $dto) {
            $this->assertInstanceOf(PMSBookingDTO::class, $dto);
            //Check that the mapping of IDs work
            $this->assertEquals(self::HOTEL_ID_MAP[$data[$index]['hotel_id']], $dto->getHotelId());
            $this->assertEquals($data[$index]['guest'], $dto->getGuest());
            $this->assertEquals($data[$index]['booking'], $dto->getBooking());
            $this->assertInstanceOf(DateTime::class, $dto->getCreated());
            $this->assertEquals($data[$index]['created'], $dto->getCreated()->format('Y-m-d H:i:s'));
            $this->assertEquals($data[$index]['signature'], $dto->getSignature());
        }

        //No Errors found during process
        $this->assertEmpty($result['errors']);
    }
}
