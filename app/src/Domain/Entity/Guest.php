<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;


class Guest
{
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    private string $name;
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    private string $lastname;
    #[Assert\Type(type: 'date')]
    #[Assert\NotBlank]
    private \DateTimeImmutable $birthdate;
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    private string $passport;
    #[Assert\Country]
    #[Assert\NotBlank]
    private string $country;


    /**
     * @return int
     */
    public function getAge(): int
    {
        $now = new DateTime();
        $interval = $now->diff($this->birthdate);
        // Return the age in years
        return $interval->y;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getBirthdate(): \DateTimeImmutable
    {
        return $this->birthdate;
    }

    /**
     * @return string
     */
    public function getPassport(): string
    {
        return $this->passport;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }
}