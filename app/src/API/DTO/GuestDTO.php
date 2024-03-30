<?php
declare(strict_types=1);


namespace App\API\DTO;

use DateTimeInterface;

class GuestDTO
{
    private string $name;
    private string $lastname;
    private DateTimeInterface $birthdate;
    private string $passport;
    private string $country;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getBirthdate(): DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(DateTimeInterface $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    public function getPassport(): string
    {
        return $this->passport;
    }

    public function setPassport(string $passport): void
    {
        $this->passport = $passport;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }
}