<?php
declare(strict_types=1);


namespace App\Infrastructure\Serializer;

use App\Domain\Entity\Guest;
use DateTime;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CustomGuestNormalizer implements NormalizerInterface
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var Guest $guest */
        $guest = clone $object;
        return [
            'name' => $guest->getName(),
            'lastname' => $guest->getLastname(),
            'birthdate' => $guest->getBirthdate()->format('Y-m-d'),
            'passport' => $guest->getPassport(),
            'country' => $guest->getCountry(),
            'age' => $this->getAge($guest->getBirthdate())
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Guest && $format === 'array';
    }

    public function getSupportedTypes(?string $format): array
    {
        // Return the supported types for normalization
        return [Guest::class => true];
    }

    /**
     * @param DateTime $birthday
     * @return int
     */
    public function getAge(DateTime $birthday): int
    {
        $currentDate = new DateTime();
        $diff = $currentDate->diff($birthday);
        return $diff->y;
    }
}