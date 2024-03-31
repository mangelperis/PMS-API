<?php

namespace App\Tests\Unit\Domain;

use App\Domain\Entity\Guest;
use PHPUnit\Framework\TestCase;

class GuestTest extends TestCase
{
    private object $sampleGuestObject;

    public function setUp(): void
    {
        $this->sampleGuestObject = (object)[
            'name' => 'Jesús',
            'lastname' => 'Delagarza',
            'birthdate' => new \DateTime('1974-08-05'),
            'passport' => 'MF-1645022-OZ',
            'country' => 'MF',
        ];
    }

    public function testConstructorAndGetters()
    {
        $guest = new Guest(
            $this->sampleGuestObject->name,
            $this->sampleGuestObject->lastname,
            $this->sampleGuestObject->birthdate,
            $this->sampleGuestObject->passport,
            $this->sampleGuestObject->country,
        );

        // Assert that the constructor sets the properties correctly
        $this->assertSame($this->sampleGuestObject->name, $guest->getName());
        $this->assertSame($this->sampleGuestObject->lastname, $guest->getLastname());
        $this->assertSame($this->sampleGuestObject->birthdate, $guest->getBirthdate());
        $this->assertSame($this->sampleGuestObject->passport, $guest->getPassport());
        $this->assertSame($this->sampleGuestObject->country, $guest->getCountry());

    }

}
