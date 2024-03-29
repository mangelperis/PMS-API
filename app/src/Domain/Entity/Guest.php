<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use DateTime;

class Guest
{
    private string $name;
    private string $lastname;
    private DateTime $birthdate;
    private string $passport;
    private string $country;
    private int $age;

    // Constructor, getters y setters
}