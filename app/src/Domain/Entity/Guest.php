<?php
declare(strict_types=1);


namespace App\Domain\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Table(name: 'guests')]
class Guest
{
    #[Assert\Type(type: 'integer')]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private string $name;
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string')]
    private string $lastname;
    #[Assert\Type(type: 'date')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'date')]
    private DateTime $birthdate;
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 13)]
    private string $passport;
    #[Assert\Country]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 2)]
    private string $country;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'created', type: Types::DATETIME_MUTABLE)]
    private DateTime $created;
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $updated;

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
     * @return DateTime
     */
    public function getBirthdate(): DateTime
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

    public function getId(): int
    {
        return $this->id;
    }
}